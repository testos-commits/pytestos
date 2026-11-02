<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Profile;
use App\Models\Attribute;
use App\Models\CastingImageType;
use App\Models\SocialMediaPlatform;
use App\Http\Resources\UserResource;
use App\Models\PaymentDetail;
use App\Services\PaymentsService;
use App\Models\ProfileImage;
use App\Services\FileDownloadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileService
{

    public function create(array $data)
    {
        $profile = Profile::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'date_of_birth' => $data['date_of_birth'],
            'country_id' => $data['country_id'],
            'phone' => $data['phone'] ?? null,
            'country_code_country_id' => $data['country_code_country_id'] ?? null,
            'gender' => $data['gender'],
            'user_id' => auth()->user()->id,
            'is_default' => 1,
            'is_first_login' => 0,
        ]);

        $profile->nationalities()->attach($data['nationality_ids']);
        $profile->passports()->attach($data['passport_ids']);

        return $this->switchProfile($profile);
    }

    public function getUser()
    {
        $userId = auth()->user()->id;
        $types = CastingImageType::all(['*', 'casting_image_types.image as reference_image']);
        $platorms = SocialMediaPlatform::all();

        $user = User::with([
            'profile.unreadProfileMessages',
            'profile.notifications',
            'profile.countryCode',
            'profiles.unreadProfileMessages',
            'profiles.notifications',
            'profile.country',
            'profile.nationalities',
            'profile.passports',
            'profiles.castingImages',
            'profile.profileImages' => function ($query) {
                $query->orderBy('id', 'desc');
            },
            'profile.profileImages',
            'profile.accepted.project',
            'paymentDetails',
            'referrals' => function ($query) {
                $query->with(['referralRewards' => function ($q) {
                    $q->where('status', 'paid');
                }]);
            },
        ])->find($userId);

        if (!$user->profile && $user->profiles->count() > 0) {
            $user->profiles->first()->update(['is_default' => 1]);

            $user = User::with([
                'profile.unreadProfileMessages',
                'profile.notifications',
                'profile.countryCode',
                'profiles.unreadProfileMessages',
                'profile.country',
                'profile.nationalities',
                'profile.passports',
                'profile.profileImages' => function ($query) {
                    $query->orderBy('id', 'desc');
                },
                'profile.profileImages',
                'profile.accepted.project',
                'profiles.notifications',
                'profiles.castingImages',
                'paymentDetails',
                'referrals' => function ($query) {
                    $query->with(['referralRewards' => function ($q) {
                        $q->where('status', 'paid');
                    }]);
                },
            ])->find($userId);
        }

        $user->profile->typedCastingImages =  $this->setTypesToCastingImages($types, $user->profile);
        $user->profile->social_media = $this->setPlatformsToAccounts($platorms, $user->profile->social_media_accounts);
        $user->profile->payments = PaymentsService::getUserWallet($user->profile);
        $user->profile->hasUnreadNotifications = $this->hasProfileNotifications($user->profile);

        $oldestId = $user->profile->id;

        $user->profiles->map(function ($profile) use ($types, &$oldestId) {
            if ($profile->id < $oldestId) {
                $oldestId = $profile->id;
            }

            $profile->hasUnreadNotifications = $this->hasProfileNotifications($profile);
            //needed for profile images to show in the account switcher on the app
            $profile->typedCastingImages = $this->setTypesToCastingImages($types, $profile);

            return $profile;
        });

        $user->profiles->map(function ($profile) use ($oldestId) {
            if ($profile->id == $oldestId) {
                $profile->is_main = true;
            } else {
                $profile->is_main = false;
            }

            return $profile;
        });

        if ($user->profile->id == $oldestId) {
            $user->profile->is_main = true;
        } else {
            $user->profile->is_main = false;
        }

        return new UserResource($user);
    }

    public function hasProfileNotifications($profile)
    {
        $lastReadNotifications = $profile->last_read_notifications ? Carbon::parse($profile->last_read_notifications) : null;

        $notifications = $profile->notifications;

        if (is_null($lastReadNotifications) && $notifications->count() > 0) {
            return 0;
        }

        $notifications = $notifications->filter(function ($notification) use ($lastReadNotifications) {
            return Carbon::parse($notification->created_at)->gt($lastReadNotifications);
        });

        return $notifications->count() > 0;
    }

    public function setPlatformsToAccounts($platforms, $accounts)
    {
        return $platforms->map(function ($platform) use ($accounts) {
            $platformClone = clone $platform;

            if ($accounts) {
                $platformClone->value = isset($accounts[$platformClone->slug]) ? $accounts[$platformClone->slug] : null;
            }

            return $platformClone;
        });
    }

    public function setTypesToCastingImages($types, $profile)
    {
        return $types->map(function ($type) use ($profile) {
            $typeClone = clone $type;

            if ($profile->castingImages) {
                $image = $profile->castingImages->firstWhere('casting_image_type_id', $typeClone->id);

                $typeClone->image = $image ? $image->image : null;
                $typeClone->casting_image_type_id = $typeClone->id;
            } else {
                $typeClone->image = null;
                $typeClone->casting_image_type_id = $typeClone->id;
            }

            return $typeClone;
        });
    }


    public function addProfile($data)
    {
        $user = User::with('profiles')->find(auth()->user()->id);
        $user->removeDefaultProfile();
        $user->profiles()->create($data);

        return $this->getUser();
    }

    public function updateInfo($data)
    {
        $attributes = Attribute::all()->pluck('slug');
        $user = User::find(auth()->user()->id);

        /** SET WHAT NEEDS TO BE IN META COLUMN */
        $attributesData = [];

        foreach ($attributes as $attribute) {
            if (isset($data[$attribute])) {
                $attributesData[$attribute] = $data[$attribute];
                unset($data[$attribute]);
            }
        }

        if ($user->profile->meta == null) {
            $user->profile->meta = [];
        }

        $data['meta'] = array_merge($user->profile->meta, $attributesData);

        /** SET NATIONALITIES */
        if (isset($data['nationality_ids'])) {
            $user->profile->nationalities()->detach();
            $user->profile->nationalities()->attach($data['nationality_ids']);
            unset($data['nationality_ids']);
        }

        /** SET PASSPORTS */
        if (isset($data['passport_ids'])) {
            $user->profile->passports()->detach();
            $user->profile->passports()->attach($data['passport_ids']);
            unset($data['passport_ids']);
        }


        $user->profile->update($data);
        return $this->getUser();
    }

    public function handleReplaceCastingImage($data)
    {
        $profile = Profile::with('castingImages', 'country')
            ->find(auth()->user()->profile->id);

        $image = $profile->castingImages->firstWhere('casting_image_type_id', $data['casting_image_type_id']);

        // in old logic images were not delete because they had the same name and were getting replaced.
        // new logic timestamps the name of photos so we have to delete the image itself
        if ($image && $image->image !== $data['url']) {
            $this->deleteImageFromS3($image->image, $profile->country->aws_bucket);
        }


        $profile->castingImages()->updateOrCreate(
            ['casting_image_type_id' => $data['casting_image_type_id']],
            ['image' => $data['url']]
        );
    }

    public function handleReplaceCastingVideo($data)
    {
        $profile = Profile::with('country')
            ->find(auth()->user()->profile->id);

        if ($profile->video && $profile->video !== $data['url']) {
            $disk = $profile->country->aws_bucket;
            
            if (str_contains($profile->video, 'promote-compression')) {
                $disk = 'compressed_videos';
            }
            $this->deleteImageFromS3($profile->video, $disk);
        }

        if ($profile->video_thumbnail && $profile->video_thumbnail !== $data['video_thumbnail']) {
            $this->deleteImageFromS3($profile->video_thumbnail, $profile->country->aws_bucket);
        }

        $profile->video = $data['url'];
        $profile->video_thumbnail = $data['video_thumbnail'];
        $profile->save();
    }

    public function saveMediaAssetURL($data)
    {
        switch ($data['type']) {
            case 'casting_image':
                // $user = User::find(auth()->user()->id);
                // $user->profile->castingImages()->updateOrCreate(
                //     ['casting_image_type_id' => $data['casting_image_type_id']],
                //     ['image' => $data['url']]
                // );
                //here
                $user = auth()->user();
                $this->handleReplaceCastingImage($data);
                break;
            case 'profile_image':
                $user = User::find(auth()->user()->id);
                $user->profile->profileImages()->create([
                    'image' => $data['url']
                ]);

                break;
            case 'video':
                // $user = User::find(auth()->user()->id);
                // $user->profile->video = $data['url'];
                // $user->profile->video_thumbnail = $data['video_thumbnail'];
                // $user->profile->save();
                //here
                $user = User::find(auth()->user()->id);
                $this->handleReplaceCastingVideo($data);
                break;
            default:
                break;
        }
        return $user;
    }


    public function deleteProfileImage(int $id)
    {
        try {
            $user = User::with('profile.country')->find(auth()->user()->id);

            $profileImage = ProfileImage::where([
                'user_profile_id' => auth()->user()->profile->id,
                'id' => $id
            ])->first();

            //here
            $this->deleteImageFromS3($profileImage->image, $user->profile->country->aws_bucket);

            $profileImage->delete();
            return $this->getUser();
        } catch (\Exception $e) {
            Log::error('Error deleting profile image: ' . $e->getMessage());
            return false;
        }
    }

    public function switchProfile($profile)
    {
        Profile::where('user_id', auth()->user()->id)
            ->update(['is_default' => 0]);

        $profile->is_default = true;
        $profile->save();

        return $this->getUser();
    }

    public function switchLanguage($language)
    {
        $supportedLanguages = config('app.supported_locales');

        if (!in_array($language, $supportedLanguages)) {
            return false;
        }

        $user = User::find(auth()->user()->id);
        $user->app_language = $language;

        $user->save();

        return true;
    }

    public static function getAllProfiles($searchQuery = '', $filters = [], $perPage)
    {
        $user = User::find(auth()->user()->id);

        $query = Profile::query();

        if ($user->hasRole('country admin')) {
            $query->whereIn('country_id', $user->countries->pluck('id'));
        }

        if (!empty($searchQuery)) {
            $query->where(function ($query) use ($searchQuery) {
                // Check if the search query is numeric, if so, treat it as an ID or phone number
                if (!(int)$searchQuery) {
                    $query->where('first_name', 'like', '%' . $searchQuery . '%')
                        ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
                        ->orWhereHas('user', function ($query) use ($searchQuery) {
                            $query->where('email', 'like', '%' . $searchQuery . '%');
                        });
                } else {
                    if (strlen($searchQuery) > 6) {
                        $query->orWhere('phone', 'like', '%' . $searchQuery . '%');
                    } else {
                        $query->where('id', (int)$searchQuery);
                    }
                }
            });
        }

        foreach ($filters as $key => $value) {
            if (is_array($value) && count($value) > 0) {
                if (strpos($key, 'meta_') !== false) {
                    $key = str_replace('meta_', '', $key);
                    $query->whereJsonContains("meta->{$key}", $value);
                }
            } else {

                if ($key == 'country_id' && $value !== null && $value !== '') {
                    $query->where('country_id', $value);
                }

                if ($key == 'nationality_id' && $value !== null && $value !== '') {
                    $query->whereHas('nationalities', function ($query) use ($value) {
                        $query->where('country_id', $value);
                    });
                }

                if ($key == 'gender' && $value !== null && $value !== '') {
                    $query->where('gender', $value);
                }

                if ($key === 'min_age' && $value !== null && $value !== '') {
                    $query->where('date_of_birth', '<=', now()->subYears($value));
                }

                if ($key === 'max_age' && $value !== null && $value !== '') {
                    $query->where('date_of_birth', '>=', now()->subYears($value));
                }
            }
        }

        return $query->paginate($perPage);
    }

    public static function getProfileForAdmin($profileId)
    {
        return Profile::with([
            'country',
            'nationalities',
            'passports',
            'castingImages',
            'profileImages',
            'user.referredBy',
            'user.referrals',
            'user.paymentDetails',
        ])->find($profileId);
    }

    public static function createPaymentDetails($data)
    {
        PaymentDetail::updateOrCreate(
            ['user_id' => auth()->user()->id],
            $data
        );
    }

    public static function addPreviousCommercials($commercial)
    {
        $user = User::find(auth()->user()->id);
        $commercials = $user->profile->previous_commercials;

        if ($commercials == null) {
            $commercials = [];
        }

        array_push($commercials, $commercial);

        $user->profile->previous_commercials = $commercials;
        $user->profile->save();

        return $user;
    }

    public function delete()
    {
        $user = User::with('profiles', 'profile.castingImages', 'profile.profileImages', 'profile.country')->find(auth()->user()->id);

        if ($user->profiles->count() > 1) {
            $newDefault = $user->profiles->firstWhere('is_default', '!=', 1);

            foreach ($user->profile->castingImages as $castingImage) {
                $this->deleteImageFromS3($castingImage->image, $user->profile->country->aws_bucket);
            }

            if ($user->profile->video) {
                $this->deleteVideoFromS3($user->profile->video);
            }

            $user->profile->delete();
            $newDefault->update(['is_default' => 1]);
        }

        return $this->getUser();
    }

    public function deleteAccount()
    {
        $user = User::with('profiles', 'profile.castingImages', 'profile.profileImages')->find(auth()->user()->id);

        foreach ($user->profiles as $profile) {
            foreach ($profile->castingImages as $castingImage) {
                $this->deleteImageFromS3($castingImage->image, $user->profile->country->aws_bucket);
            }

            if ($profile->video) {
                $this->deleteVideoFromS3($profile->video);
            }
        }

        $user->delete();

        return true;
    }


    /**
     * Summary of deleteImageFromS3
     * @param string $image FULL AWS IMAGE URL 
     * @param mixed $aws_disk
     * @return void
     */
    private function deleteImageFromS3($image, $aws_disk)
    {

        $path = parse_url($image, PHP_URL_PATH);
        $key = ltrim($path, '/');
        if ($aws_disk !== 'default') {
            // In database, we don't use the s3_ prefix for country bucket configuration,
            //but in filesystems.php we do, this solves the problem
            $aws_disk = "s3_$aws_disk";
        }

        if (Storage::disk($aws_disk)->exists($key)) {
            Storage::disk($aws_disk)->delete($key);
        }
    }

    public function deleteVideoFromS3($video)
    {
        $disk = FileDownloadService::getVideoBucketConfig($video);

        if (!$disk) {
            return;
        }

        $fileName = str_replace(
            config("filesystems.disks.$disk.url"),
            '',
            $video
        );

        if (Storage::disk($disk)->exists($fileName)) {
            Storage::disk($disk)->delete($fileName);
        }
    }
}
