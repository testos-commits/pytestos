<?php

namespace App\Services;

use App\Models\User;
use App\Models\Profile;
use App\Models\CastingImage;
use App\Models\CastingImageType;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use App\Models\OldUser;
use App\Models\OldUserImage;
use App\Models\OldUserImageType;
use App\Models\OldNationality;
use App\Models\OldSocialMediaPlatform;
use App\Models\SocialMediaPlatform;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DatabaseMigrationService
{

    public $activeCountries = [1 => '961', 2 => '971', 3 => '995', 4 => '974'];
    public $countryMap = [];
    public $nationalitiesMap = [];
    public $skills = [];

    public function migrateDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            $this->importCountries();
        } catch (\Exception $e) {
            Log::channel('migration')->error('Error importing countries: ' . $e->getMessage());
        }

        try {
            $this->retrieveNationalities();
        } catch (\Exception $e) {
            Log::channel('migration')->error('Error retrieving nationalities: ' . $e->getMessage());
        }

        try {
            $this->migrateSocialMediaPlatforms();
        } catch (\Exception $e) {
            Log::channel('migration')->error('Error retrieving social media platforms: ' . $e->getMessage());
        }

        try {
            $this->migrateCastingImageTypes();
        } catch (\Exception $e) {
            Log::channel('migration')->error('Error retrieving casting image types: ' . $e->getMessage());
        }

        try {
            $this->migrateUsersAndProfiles();
        } catch (\Exception $e) {
            Log::channel('migration')->error('Error migrating users and profiles: ' . $e->getMessage());
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function importCountries()
    {
        DB::table('countries')->truncate();
        //read from a json file and import into db
        if (!file_exists(base_path('resources/json/countries.json'))) {
            throw new \Exception('Countries json file not found');
        }

        $countries = json_decode(file_get_contents(base_path('resources/json/countries.json')), true);

        foreach ($countries as $country) {
            $is_active = in_array($country['phone_code'], $this->activeCountries) ? 1 : 0;

            $newCountry =  Country::create([
                'name' => ['en' => $country['english_name'], 'ar' => $country['arabic_name']],
                'country_code' => $country['phone_code'],
                'is_active' => $is_active
            ]);

            $countryId = array_search($country['phone_code'], $this->activeCountries);

            if ($is_active) {
                $this->countryMap[$countryId] = $newCountry->id;
            }
        }
    }

    public function migrateSocialMediaPlatforms()
    {
        DB::table('social_media_platforms')->truncate();

        $socialMediaPlatforms = OldSocialMediaPlatform::all();
        $data = [];

        foreach ($socialMediaPlatforms as $platform) {
            $data[] = [
                'slug' => Str::slug($platform->name),
                'name' =>  json_encode(['ar' => $platform->name, 'en' => $platform->name]),
                'created_at' => $platform->created_at,
                'updated_at' => $platform->updated_at
            ];
        }

        SocialMediaPlatform::insert($data);
    }

    public function migrateCastingImageTypes()
    {
        DB::table('casting_image_types')->truncate();

        $imageTypes = OldUserImageType::all();

        foreach ($imageTypes as $type) {
            CastingImageType::create([
                'name' => ['ar' => $type->name, 'en' => $type->name],
                'description' => ['ar' => $type->name, 'en' => $type->name],
            ]);
        }
    }
    public function migrateUsersAndProfiles()
    {
        DB::table('users')->truncate();
        DB::table('user_profiles')->truncate();
        DB::table('expo_tokens')->truncate();
        DB::table('profile_casting_images')->truncate();
        DB::table('profile_nationalities')->truncate();

        $oldUsers = OldUser::with('images.type', 'nationality', 'socialMediaAccounts')->get();


        foreach ($oldUsers as $oldUser) {
            $newUser = User::create([
                'email' => $oldUser->email,
                'password' => $oldUser->password,
                'email_verified_at' => $oldUser->email_verified_at,
                'remember_token' => $oldUser->remember_token,
                'created_at' => $oldUser->created_at,
                'updated_at' => $oldUser->updated_at
            ]);

            //attach expo token to user
            // if ($oldUser->expo_token) {
            //     $newUser->expoToken()->create([
            //         'value' => $oldUser->expo_token
            //     ]);
            // }

            $socialAccounts = [];

            foreach ($oldUser->socialMediaAccounts as $account) {
                $socialAccounts[Str::slug($account->socialMediaPlatform->name)] = $account->value;
            }

            $newProfile =  Profile::create([
                'user_id' => $newUser->id,
                'old_user_id' => $oldUser->id, // used to reference s3 directory images
                'first_name' => $oldUser->firstname,
                'last_name' => $oldUser->lastname,
                'description' => $oldUser->description,
                'country_id' => $this->countryMap[$oldUser->country_id],
                'date_of_birth' => $oldUser->date_of_birth,
                'city' => $oldUser->city,
                'street' => $oldUser->street,
                'building' => $oldUser->building,
                'phone' => $oldUser->mobile,
                'gender' => $oldUser->gender,
                'major' => $oldUser->major,
                'university' => $oldUser->university,
                'occupation' => $oldUser->occupation,
                'weight' => $oldUser->weight,
                'height' => $oldUser->height,
                'shoes' => $oldUser->shoes_size,
                'pants' => $oldUser->pants_size,
                'shirt' => $oldUser->shirt_size,
                'social_media_accounts' => count($socialAccounts) > 0 ? $socialAccounts : null,
                'is_default' => 1,
                'video' => $oldUser->compressed_video ? $oldUser->compressed_video : $oldUser->user_video,
                'is_first_login' => 1,
            ]);


            if ($oldUser->nationality_id && in_array($oldUser->nationality_id, array_keys($this->nationalitiesMap))) {
                try {
                    $newProfile->nationalities()->attach($this->nationalitiesMap[$oldUser->nationality_id]);
                } catch (\Exception $e) {
                    Log::channel('migration')->error('Error attaching Nationality: ' . $e->getMessage(), ['old_user_id' => $oldUser->id, ';nationalitiesMap' => $this->nationalitiesMap]);
                }
            }


            $castingImages = [];
            foreach ($oldUser->images as $image) {
                if (!$image->s3_url) {
                    continue;
                }
                $castingImages[] = [
                    'user_profile_id' => $newProfile->id,
                    'casting_image_type_id' => $image->type->id,
                    'image' => $image->s3_url,
                ];
            }

            CastingImage::insert($castingImages);
        }

        User::create([
            'email' => 'admin@admin.com',
            'password' => '$2y$12$.1gAtSP8u/Dw8dXZwprzF.8J.IEL3tgdUM0lW4emcnelqInLjfvb6',
            'email_verified_at' => '2021-07-01 00:00:00',
            'is_admin' => 1,
        ]);
    }

    public function retrieveNationalities()
    {
        $uniqueNationalities = OldNationality::whereIn('id', function ($query) {
            $query->select('nationality_id')
                ->distinct()
                ->from('users');
        })->get();

        foreach ($uniqueNationalities as $nationality) {
            $country = Country::whereJsonContains('name', ['en' => $nationality->name])->first();
            if ($country) {
                $this->nationalitiesMap[$nationality->id] = $country->id;
            }
        }
    }
    // public function retrieveSkills()
    // {
    //     $oldUsers = OldUser::with('images.type')->get();

    //     foreach ($oldUsers as $oldUser) {
    //         if ($oldUser->skills_1) {
    //             $this->skills[] = $oldUser->skills_1;
    //         }

    //         if ($oldUser->skills_2) {
    //             $this->skills[] = $oldUser->skills_2;
    //         }

    //         if ($oldUser->skills_3) {
    //             $this->skills[] = $oldUser->skills_3;
    //         }

    //         if ($oldUser->skills_4) {
    //             $this->skills[] = $oldUser->skills_4;
    //         }
    //     }

    //     return array_unique($this->skills);
    // }
}
