<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Database\Seeders\AddProfilesToUserSeeder;
use Database\Seeders\UserWithProfilesSeeder;

class CreateUserWithProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an account with its profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email =  $this->ask("What is the account's email?");

        $user2 = null;

        if ($email) {
            $user = User::where('email', $email)->first();
        } else {
            $user = User::where('email', 'test@seeder.com')->first();
        }


        if ($user) {
            $actionAnswer = $this->choice(
                'User exists, what do you want to do?',
                [
                    'Add new profiles to the existing user',
                    'Overwrite current user and accounts',
                    'Exit'
                ],
                0
            );
            switch ($actionAnswer) {
                case 'Add new profiles to the existing user':
                    $countryId = $this->ask('What is the countryId for the profiles?');

                    $seeder = new AddProfilesToUserSeeder();
                    $seeder->run($countryId, $user);
                    $this->info('Profiles added to user successfully!');
                    break;

                case 'Overwrite current user and accounts':
                    $user->delete();
                    $this->createFreshAccount($email);
                    break;

                case 'Exit':
                    $this->info('Exited!');
                    return;
            }
        } else {
            $this->createFreshAccount($email);
        }
    }


    public function createFreshAccount($email)
    {
        $countryId = $this->ask('What is the countryId for the profiles?');
        $profilesCount = $this->ask('How many profiles do you want to create?');

        $seeder = new UserWithProfilesSeeder();
        $seeder->run($countryId, $profilesCount, $email);

        $this->info('User with profiles created successfully!');
    }
}
