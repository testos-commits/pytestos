<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** RESET DB */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

        foreach ($tableNames as $name) {
            if ($name == 'migrations') {
                continue;
            }
            DB::table($name)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /** SETUP BASE DATA */
        $availableCountries = \App\Models\Country::factory(4)->create();
        $attributes = \App\Models\Attribute::factory()->count(5)->create();
        $countries = \App\Models\Country::factory(10)->create();
        $adminUser = \App\Models\User::factory()->create(['is_admin' => true]);
        $directorUser = \App\Models\User::factory()->create(['is_director' => true]);
        $partnerUser = \App\Models\User::factory()->create(['is_partner' => true]);

        $castingImageTypes = \App\Models\CastingImageType::factory()->count(4)->create();

        $partnerUser->countries()->attach($availableCountries->random(3));
        $directorUser->countries()->attach($availableCountries->random(3));
        $adminUser->countries()->attach($availableCountries->random(3));

        $users = \App\Models\User::factory(10)
            ->has(\App\Models\Profile::factory()->count(3), 'profiles')
            ->create(['is_director' => false, 'is_admin' => false, 'is_partner' => false]);

        /** SETUP TEST USER SO WE DON'T KEEP REGISTERING */
        \App\Models\User::factory()
            ->has(\App\Models\Profile::factory()->count(3), 'profiles')
            ->create([
                'email' => 'joe@yllwdigital.com',
                'password' => bcrypt('password'),
                'is_director' => false,
                'is_admin' => false,
                'is_partner' => false
            ]);

        $users = \App\Models\User::whereHas('profiles')->get();

        $projects = \App\Models\Project::factory(10)
            ->has(\App\Models\Audition::factory()->count(5), 'auditions')
            ->create(['user_id' => $partnerUser->id]);

        $createdAuditions = \App\Models\Audition::all();

        /** SETUP AUDITION FILTERS, APPLICATIONS & MESSAGES */
        foreach ($createdAuditions as $audition) {
            $auditionAttributes = $attributes->random(3);
            $selectedAttributes = [];

            foreach ($auditionAttributes as $attribute) {
                $index = array_rand($attribute->options, 2);
                $selectedAttributes[$attribute->slug] = $index;
            }

            $audition->country_id = $partnerUser->countries->random()->id;
            $audition->meta = $selectedAttributes;
            $audition->save();

            $audition->applications()->createMany([
                [
                    'user_profile_id' => $users->random()->profile->id,
                    'is_shortlisted' => false,
                    'is_accepted' => false,
                ],
                [
                    'user_profile_id' => $users->random()->profile->id,
                    'is_shortlisted' => false,
                    'is_accepted' => false,
                ],
                [
                    'user_profile_id' => $users->random()->profile->id,
                    'is_shortlisted' => false,
                    'is_accepted' => false,
                ],
            ]);

            \App\Models\Message::factory(4)->has(\App\Models\MessageOption::factory()->count(3), 'options')
                ->create([
                    'audition_id' => $audition->id,
                    'for_applicants' => rand(0, 1),
                    'for_shortlisted' => rand(0, 1),
                    'for_accepted' => rand(0, 1),
                ]);
        }

        /** SETUP PROFILE DETAILS */
        foreach ($users as $user) {
            foreach ($user->profiles as $k => $profile) {
                if ($k > 0) { //make sure only 1 profile is the default profile
                    $profile->is_default = 0;
                    $profile->save();
                }
                $profileAttributes = $attributes->random(3);
                $selectedAttributes = [];

                foreach ($profileAttributes as $attribute) {
                    $index = array_rand($attribute->options, 2);
                    $selectedAttributes[$attribute->slug] = $index;
                }
                $profile->castingImages()->createMany([
                    [
                        'image' => 'https://picsum.photos/200/300',
                        'casting_image_type_id' => $castingImageTypes->random()->id,
                    ],
                    [
                        'image' => 'https://picsum.photos/200/300',
                        'casting_image_type_id' => $castingImageTypes->random()->id,
                    ],
                    [
                        'image' => 'https://picsum.photos/200/300',
                        'casting_image_type_id' => $castingImageTypes->random()->id,
                    ],
                ]);
                $profile->country_id = $availableCountries->random()->id;
                $profile->meta = $selectedAttributes;
                $profile->save();
            }
        }

        /** SETUP MESSAGE FOR EACH USER */
        $applications = \App\Models\AuditionApplication::with('audition.messages.options', 'profile')
            ->get();

        foreach ($applications as $application) {
            $audition = $application->audition;
            $profile = $application->profile;
            $message = $audition->messages->random();
            $option = $message->options->random();

            $profile->messages()->attach($message->id, [
                'audition_messages_option_id' => $option->id,
                'audition_message_id' => $message->id,
                'read' => 0
            ]);
        }
    }
}
