<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class AddProfilesToUserSeeder extends Seeder
{

    public function run($countryId, User $user): void
    {
        $profile = Profile::factory()->make([
            'user_id' => $user->id,
            'country_id' => $countryId,
        ]);

        $user->profiles()->save($profile);

        $castingImages = \App\Models\CastingImage::factory(3)->make();

        $profile->castingImages()->saveMany($castingImages);
    }
}
