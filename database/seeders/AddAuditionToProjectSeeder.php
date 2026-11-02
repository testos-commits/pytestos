<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Audition;

class AddAuditionToProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($projectId, $country): void
    {
      $audition = Audition::factory()->create([
            'project_id' => $projectId
        ]);

        $audition->countries()->attach($country);
    }
}
