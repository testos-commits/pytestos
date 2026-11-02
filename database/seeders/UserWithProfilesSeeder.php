<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Country;

class UserWithProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($countryId = false, $profileCount = false, $email): void
    {
        $countryPlaceholder = Country::first();

        User::factory()
            ->hasProfiles($profileCount ?: 2, [
                'country_id' => $countryId ?: 1,
                'is_first_login' => false,
            ])
            ->create([
                'email' => $email,
                'password' => bcrypt('password'),
            ])
            ->profiles
            ->each(function ($profile, $key) use ($countryPlaceholder) {
                if ($key === 0) {
                    $profile->update([
                        'is_default' => true,
                    ]);
                }

                $profile->nationalities()->attach($countryPlaceholder->id);
                $profile->passports()->attach($countryPlaceholder->id);

                $castingImages = \App\Models\CastingImage::factory(3)->make();
                $profile->castingImages()->saveMany($castingImages);
            });
    }
}
