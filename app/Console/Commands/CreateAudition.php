<?php

namespace App\Console\Commands;

use Database\Seeders\AddAuditionToProjectSeeder;
use Illuminate\Console\Command;

class CreateAudition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audition:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an audition for a pre-existing project';

    /**
     * Execute the console command.
     */
    public function handle() {
        $this->create();
    }

    public function create()
    {
        $projectId = $this->ask('What is the projectId for this audition?');
        $country = $this->ask('Which country should the audition be available in? ');


        $seeder = new AddAuditionToProjectSeeder();
        $seeder->run($projectId, $country);

        $this->info('Audition added to project succesfully!');
    }
}
