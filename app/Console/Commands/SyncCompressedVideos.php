<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Profile;
use ErrorException;

class SyncCompressedVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    // add countires argument

    protected $signature = 'app:sync-compressed-videos {--rollback=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs the compressed videos to the old users';

    public $table = 'videos_to_compress';
    public $compressedVideoUrl = 'compressed_video_url';
    public $rawVideoUrl = 'video';

    /**0
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('rollback') === 'true') {
            $this->info('Rolling back compressed videos to raw videos...');
            $this->rollback();
            return;
        }

        $this->info('Syncing compressed videos to profiles...');
        $this->sync();
    }


    public function loadProfiles()
    {
        try {
            $compressionData = DB::table($this->table)
                ->whereNotNull($this->compressedVideoUrl)
                ->get();

            $profileIds = $compressionData->pluck('id');

            $profiles = Profile::whereIn('id', $profileIds)
                ->get();

            if ($profiles->isEmpty()) {
                $this->info('No rows to sync');
                return;
            }

            return [
                'compressed' => $compressionData,
                'current' => $profiles
            ];
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    public function sync()
    {
        $result = $this->loadProfiles();

        foreach ($result['current'] as $profile) {
            $compressionData = $result['compressed']->firstWhere('id', $profile->id);
            $profile->video = $compressionData->{$this->compressedVideoUrl};
            $profile->save();
        }

        $this->info("Sync completed! Total profiles updated: " . $result['current']->count());
    }

    public function rollback()
    {
        $result = $this->loadProfiles();

        foreach ($result['current'] as $profile) {
            $compressionData = $result['compressed']->firstWhere('id', $profile->id);
            $profile->video = $compressionData->{$this->rawVideoUrl};
            $profile->save();
        }

        $this->info('Rollback completed!');
    }
}
