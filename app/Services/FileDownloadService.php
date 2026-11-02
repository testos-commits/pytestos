<?php

namespace App\Services;

use App\Exports\ProfilesExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use App\Models\Profile;
use App\Models\UserDownloadRequest;
use App\Notifications\DownloadReadyNotification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class FileDownloadService
{
    public $profiles;
    public $profileIds;
    public $audition;
    public $user;
    public $tempDirectory;
    public $parentDirectory;
    public $hash;
    public $awsConfig = null;
    public $s3CastingImagesDisk = null;
    public $s3VideoDisk = null;


    public function __construct($profileIds, $audition, $user)
    {
        $this->profileIds = $profileIds;
        $this->audition = $audition;
        $this->user = $user;

        $filename = Str::snake($this->audition->project->title . '-' . $this->audition->title);

        $this->parentDirectory = 'temp/' . uniqid($filename, true);
    }

    public function prepareProfiles()
    {
        $this->profiles = Profile::whereIn('id', $this->profileIds)
            ->with('castingImages', 'user', 'profileImages', 'nationalities', 'passports', 'country')
            ->get();

        return $this;
    }

    public function downloadAndZipUsersContent()
    {
        foreach ($this->profiles as $profile) {
            $this->s3CastingImagesDisk = $profile->country->aws_bucket ? $profile->country->aws_bucket : 'default';

            // create the directories where the files will be temporarily stored for this user, prior to zipping
            $directories = $this->prepareDestinationDirectories($profile);

            $this->downloadProfileCastingImages(
                $profile,
                $directories
            );

            // Download subscribed images only if user is subscribed
            if ($profile->user->isSubscribed() || $profile->user->isFreePeriod()) {
                $this->downloadProfileImages($profile, $directories);
            }

            $this->configureVideoS3Disk($profile->video);

            if ($this->s3VideoDisk !== null) {
                $this->downloadVideo(
                    $profile,
                    $directories
                );
            }
        }

        $this->exportProfilesDataToCsv();
        $this->zipParentDirectory();
        $this->tempDirectory = $this->parentDirectory . '.zip';
        return $this;
    }

    public function exportProfilesDataToCsv($fileName = 'talent-info.xlsx')
    {
        return Excel::store(new ProfilesExport($this->profiles), $this->parentDirectory . '/' . $fileName, 'local');
    }

    public function prepareDestinationDirectories($profile)
    {
        $userDirectory = $this->parentDirectory . '/' . $profile->first_name . '-' . $profile->id;
        $imageDirectory = $userDirectory;
        $subscribedImagesDirectory = $userDirectory . '/extra';
        $videoDirectory = $userDirectory;

        Storage::makeDirectory($userDirectory);
        Storage::makeDirectory($subscribedImagesDirectory);

        return [
            'images' => $imageDirectory,
            'video' => $videoDirectory,
            'subscribed' => $subscribedImagesDirectory,
        ];
    }

    public function downloadProfileCastingImages($profile, $directories)
    {
        foreach ($profile->castingImages as $key => $image) {
            $fileName = ltrim(parse_url($image->image, PHP_URL_PATH), '/');
            $filePath = $directories['images'] . '/' . basename('#' . $profile->id . '-' . $profile->first_name . '_image_' . $key . '.jpg');

            try {
                if (Storage::disk($this->s3CastingImagesDisk)->exists($fileName)) {
                    $stream = Storage::disk($this->s3CastingImagesDisk)->readStream($fileName);
                    Storage::writeStream($filePath, $stream);
                }
            } catch (\Exception $e) {
                Log::warning('FileDownloadService: failed to download casting image', [
                    'profile_id' => $profile->id,
                    'file'       => $fileName,
                    'error'      => $e->getMessage(),
                ]);
            }
        }
    }

    public function downloadProfileImages($profile, $directories)
    {
        foreach ($profile->profileImages as $key => $image) {
            $fileName = ltrim(parse_url($image->image, PHP_URL_PATH), '/');
            $filePath = $directories['subscribed'] . '/' . basename($profile->first_name . '_image_' . $key . '.jpg');

            // Resolve the correct S3 disk for this image URL (may differ from casting images disk)
            $disk = self::getVideoBucketConfig($image->image) ?? $this->s3CastingImagesDisk;

            try {
                if (Storage::disk($disk)->exists($fileName)) {
                    $stream = Storage::disk($disk)->readStream($fileName);
                    Storage::writeStream($filePath, $stream);
                }
            } catch (\Exception $e) {
                Log::warning('FileDownloadService: failed to download profile image', [
                    'profile_id' => $profile->id,
                    'file'       => $fileName,
                    'error'      => $e->getMessage(),
                ]);
            }
        }
    }

    public function configureVideoS3Disk($video)
    {
        $this->s3VideoDisk = self::getVideoBucketConfig($video);
    }

    public static function getVideoBucketConfig($video): string | null
    {
        foreach (config('filesystems.disks') as $k => $fs) {
            if (empty($fs['url'])) continue;

            if (strpos($video, $fs['url']) !== false) {
                return $k;
            }

            if (empty($fs['region'])) continue;
            $pos = strpos($fs['url'], '.amazonaws.com');

            // Only try to inject region if pattern is present and URL doesn't already contain it
            if ($pos !== false && strpos($fs['url'], $fs['region']) === false) {
                $fsUrlWithRegion = substr_replace(
                    $fs['url'],
                    '.' . $fs['region'],
                    $pos,
                    0
                );

                if (strpos($video, $fsUrlWithRegion) !== false) {
                    return $k;
                }
            }
        }

        return null;
    }

    public function downloadVideo($profile, $directories)
    {
        $fileName = ltrim(parse_url($profile->video, PHP_URL_PATH), '/');

        try {
            if (Storage::disk($this->s3VideoDisk)->exists($fileName)) {
                $stream = Storage::disk($this->s3VideoDisk)->readStream($fileName);
                Storage::writeStream(
                    $directories['video'] . '/' . $profile->first_name . '_video.mp4',
                    $stream
                );
            }
        } catch (\Exception $e) {
            Log::warning('FileDownloadService: failed to download video', [
                'profile_id' => $profile->id,
                'file'       => $fileName,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    public function zipParentDirectory()
    {
        $zip = new ZipArchive();
        $zipPath = storage_path('app/' . $this->parentDirectory . '.zip');

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            // Function to recursively add files to the zip
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(storage_path('app/' . $this->parentDirectory)),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            $basePath = realpath(storage_path('app/' . $this->parentDirectory));

            foreach ($files as $file) {
                // Skip directories (they would be added automatically)
                if (!$file->isDir()) {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($basePath) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
        }

        // Optionally, delete temporary directories after zipping
        Storage::deleteDirectory($this->parentDirectory);
    }

    public function addToUserDownloads()
    {
        $this->hash = md5($this->tempDirectory . $this->user->id);
        UserDownloadRequest::create([
            'audition_id' => $this->audition->id,
            'zip_path' => $this->tempDirectory,
            'user_id' => $this->user->id,
            'hash' => $this->hash,
        ]);

        return $this;
    }

    public function notifyAdmin()
    {
        $this->user->notify(new DownloadReadyNotification($this->hash));
    }


    public static function downloadZip($hash)
    {
        $downloadRequest = UserDownloadRequest::where('hash', $hash)->first();

        if (!$downloadRequest) {
            abort(404);
        }

        $filePath = storage_path('app/' . $downloadRequest->zip_path);

        if (file_exists($filePath)) {
            $downloadRequest->is_downloaded = true;
            $downloadRequest->save();

            return response()->download($filePath)->deleteFileAfterSend(true);
        } else {
            abort(404);
        }
    }
}
