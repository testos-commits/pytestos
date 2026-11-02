<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FileDownloadService;
use Illuminate\Support\Str;

class ProcessProfilesDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 600;
    public $tries = 3;
    public $backoff = [30, 120];

    public $profileIds;
    public $audition;
    public $user;
    /**
     * Create a new job instance.
     */
    public function __construct($profileIds, $audition, $user)
    {
        $this->profileIds = $profileIds;
        $this->audition = $audition;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $FDService = new FileDownloadService($this->profileIds, $this->audition, $this->user);
        $FDService->prepareProfiles()
            ->downloadAndZipUsersContent()
            ->addToUserDownloads()
            ->notifyAdmin();
    }


    /**
     * Clean up temp files if the job fails.
     */
    public function failed(\Throwable $exception): void
    {
        $filename = Str::snake($this->audition->project->title . '-' . $this->audition->title);
        // We can't access the exact uniqid path, but the service stores it; best effort: log
        \Illuminate\Support\Facades\Log::error('ProcessProfilesDownload failed', [
            'filename' => $filename,
            'audition_id' => $this->audition->id,
            'user_id'     => $this->user->id,
            'error'       => $exception->getMessage(),
        ]);
    }
}
