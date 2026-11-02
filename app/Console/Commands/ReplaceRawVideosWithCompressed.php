<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ReplaceRawVideosWithCompressed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:replace-s3-videos-base-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command  replaces the raw video URLs in the profiles with compressed video URLs if they exist in the S3 bucket.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $eligibleProfiles = Profile::where('video', 'like', "%" . env('AWS_RAW_VIDEO_BUCKET_URL') . "%")
            ->where('video', 'not like', '%compressed%')
            ->limit(5)
            ->get();

        foreach ($eligibleProfiles as $profile) {
            $videoPath = $profile->video;
            $fileName = str_replace(
                env('AWS_RAW_VIDEO_BUCKET_URL'),
                '',
                $profile->video
            );

            $compressedVideoPath = str_replace(
                env('AWS_RAW_VIDEO_BUCKET_URL'),
                env('AWS_COMPRESSED_VIDEO_BUCKET_URL'),
                $videoPath
            );

            if (Storage::disk('s3_compressed_videos')->exists($fileName)) {
                $profile->video = $compressedVideoPath;
                $profile->save();
                $this->info("Updated profile ID {$profile->id} with compressed video: {$compressedVideoPath}");
                Log::channel('replace-raw-videos-with-compressed')->info(
                    'UPDATE SUCCESSFUL',
                    [
                        'profile_id' => $profile->id,
                        'old_video' => $videoPath,
                        'new_video' => $compressedVideoPath,
                        'compressed_video_exists' => true,
                        'ffile_name' => $fileName,
                    ]
                );
            } else {
                $this->warn("Compressed video not found for profile ID {$profile->id}: {$compressedVideoPath}");
                Log::channel('replace-raw-videos-with-compressed')->info(
                    'ERROR',
                    [
                        'profile_id' => $profile->id,
                        'old_video' => $videoPath,
                        'new_video' => $compressedVideoPath,
                        'compressed_video_exists' => false,
                        'ffile_name' => $fileName,
                    ]
                );
            }
        }
    }
}
