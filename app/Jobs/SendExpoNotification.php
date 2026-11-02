<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\ExpoNotificationService;

class SendExpoNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (config('notifications.enabled.notifications_enabled')) {
            Log::channel('notifications')->info('Sent production push notification' . ' ' . now()->toDateTimeString());

            ExpoNotificationService::send($this->data);
        } else {
            Log::channel('notifications')->info('Sent sandbox push notification' . ' ' . now()->toDateTimeString());
        }
    }
}
