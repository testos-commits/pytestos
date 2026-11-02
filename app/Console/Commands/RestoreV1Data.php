<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RestoreV1Data extends Command
{
    protected $signature = 'db:restore-from-s3';
    protected $description = 'Download and import database tables from S3 backups';

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

    public function handle()
    {

        $s3Disk = Storage::disk('s3_database_backups');
        $localDir = storage_path('imports');

        if (!is_dir($localDir)) {
            mkdir($localDir, 0755, true);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Disable foreign key checks globally

        foreach ($this->tablesToExport as $tableName) {
            $tableName = trim($tableName);
            $s3Path = "{$tableName}.sql";
            $localPath = "{$localDir}/{$tableName}.sql";

            try {
                // Download from S3
                if ($s3Disk->exists($s3Path)) {
                    file_put_contents($localPath, $s3Disk->get($s3Path));
                    $this->info("Downloaded backup for $tableName.");
                } else {
                    $this->error("Backup not found for $tableName.");
                    continue;
                }

                // Truncate the table
                DB::statement("TRUNCATE TABLE `$tableName`;");

                // Import table
                $this->importTable($tableName, $localPath);

                // Cleanup
                unlink($localPath);
                $this->info("Imported $tableName successfully.");
            } catch (\Exception $e) {
                $this->error("Failed to import $tableName: {$e->getMessage()}");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Re-enable foreign key checks globally

        return Command::SUCCESS;
    }

    protected function importTable($tableName, $filePath)
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $port = env('DB_PORT');

        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception("Import failed: " . implode("\n", $output));
        }
    }
}
