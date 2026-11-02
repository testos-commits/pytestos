<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabaseTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-v1-migrated-data-to-s3';
    protected $description = 'Export database tables to SQL and upload them to S3';

    private $tablesToExport = [
        'countries',
        'casting_image_types',
        'users',
        'user_profiles',
        'profile_nationalities',
        'profile_passports',
        'profile_casting_images',
        'social_media_platforms'
    ];

    /** 
     * The console command description.
     *
     * @var string
     */


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $tables = explode(',', $this->argument('tables')); // Get the table names
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');

        $tempDir = storage_path('exports/sql');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        foreach ($this->tablesToExport as $tableName) {
            $tableName = trim($tableName);
            $filePath = "$tempDir/{$tableName}.sql";

            // Export table to SQL
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s %s > %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($tableName),
                escapeshellarg($filePath)
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error("Failed to export table: $tableName");
                continue;
            }

            $this->info("Exported table: $tableName to $filePath");

            // Upload to S3
            $s3Path = "{$tableName}.sql";
            if (Storage::disk('s3_database_backups')->put($s3Path, file_get_contents($filePath), 'private')) {
                $this->info("Uploaded $tableName to S3 at $s3Path");
            } else {
                $this->error("Failed to upload $tableName to S3.");
            }

            // Clean up local file
            unlink($filePath);
        }

        return Command::SUCCESS;
    }
}
