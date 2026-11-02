<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseMigrationService;

class MigrateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    // add countires argument

    protected $signature = 'app:migrate-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dbs = new DatabaseMigrationService();

        $dbs->migrateDatabase();
    }
}
