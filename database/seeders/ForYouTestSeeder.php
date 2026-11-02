<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForYouTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = \App\Models\Attribute::factory()->count(5)->create();

        $meta = [
            '1_attribute_1_option' => [
                $attributes[0]->slug => [
                    $this->key($attributes[0]->options)[0],
                ],
            ],
            '1_attribute_2_options' => [
                $attributes[0]->slug => [
                    $this->key($attributes[0]->options)[0],
                    $this->key($attributes[0]->options)[1]
                ],
            ],
            '2_attributes_2_options' => [
                $attributes[0]->slug => [
                    $this->key($attributes[0]->options)[0],
                    $this->key($attributes[0]->options)[1]
                ],
                $attributes[1]->slug => [
                    $this->key($attributes[1]->options)[0],
                    $this->key($attributes[1]->options)[1]
                ],
            ],
            'wrong_attributes' => [
                $attributes[2]->slug => [
                    $this->key($attributes[2]->options)[1],
                    $this->key($attributes[2]->options)[2]
                ],
                $attributes[3]->slug => [
                    $this->key($attributes[3]->options)[0],
                    $this->key($attributes[3]->options)[1]
                ],
            ],

        ];

        $countries = \App\Models\Country::factory(1)->create();

        $adminUser = \App\Models\User::factory()->create(['is_admin' => true]);
        $adminUser->countries()->attach($countries->first());

        \App\Models\Subscription::factory()->hasAttached(
            \App\Models\User::factory()->has(
                \App\Models\Profile::factory()->state([
                    'is_default' => true,
                    'country_id' => $countries->first()->id,
                ])
            )
                ->create(['email' => '1@1.com']),
            [
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ]
        )
            ->hasAttached(
                \App\Models\User::factory()->has(
                    \App\Models\Profile::factory()->state([
                        'is_default' => true,
                        'country_id' => $countries->first()->id,
                        'meta' => $meta['1_attribute_1_option']
                    ])
                )->create(['email' => '2@2.com']),
                [
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                ],
            )
            ->hasAttached(
                \App\Models\User::factory()->has(
                    \App\Models\Profile::factory()->state([
                        'is_default' => true,
                        'country_id' => $countries->first()->id,
                        'meta' => $meta['1_attribute_2_options']
                    ])
                )->create(['email' => '3@3.com']),
                [
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                ],
            )
            ->hasAttached(
                \App\Models\User::factory()->has(
                    \App\Models\Profile::factory()->state([
                        'is_default' => true,
                        'country_id' => $countries->first()->id,
                        'meta' => $meta['2_attributes_2_options']
                    ])
                )->create(['email' => '4@4.com']),
                [
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                ],
            )
            ->hasAttached(
                \App\Models\User::factory()->has(
                    \App\Models\Profile::factory()->state([
                        'is_default' => true,
                        'country_id' => $countries->first()->id,
                        'meta' => $meta['wrong_attributes']
                    ])
                )->create(['email' => '5@5.com']),
                [
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                ],
            )
            ->create();


        $project = \App\Models\Project::factory()->create()->first();


        $audition1 = \App\Models\Audition::factory()->create([
            'title' => ['en' =>  'no_meta'],
            'project_id' => $project->id,
        ]);



        $audition2 = \App\Models\Audition::factory()->create([
            'title' => ['en' =>  '1_attribute_2_options'],
            'project_id' => $project->id,
            'meta' => $meta['1_attribute_2_options']
        ]);

        $audition3 = \App\Models\Audition::factory()->create([
            'title' => ['en' =>  '2_attributes_2_options'],
            'project_id' => $project->id,
            'meta' => $meta['2_attributes_2_options']
        ]);


        $audition4 = \App\Models\Audition::factory()->create([
            'title' => ['en' =>  '1_attribute_1_option'],
            'project_id' => $project->id,
            'meta' => $meta['1_attribute_1_option']
        ]);

        $auditions = [
            $audition1,
            $audition2,
            $audition3,
            $audition4
        ];

        foreach ($auditions as $audition) {
            \App\Models\AuditionCountry::create([
                'audition_id' => $audition->id,
                'country_id' => $countries->first()->id
            ]);
        }
    }

    public function key($options)
    {
        return array_keys($options);
    }
}
