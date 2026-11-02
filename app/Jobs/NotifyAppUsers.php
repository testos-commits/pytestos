<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Notifications\NotificationsFactory;

class NotifyAppUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $recipients = false;
    public $sendMode = false;
    public $sendModeOptions = [
        'all',
        'some',
        'countries',
        'audition_country'
    ];

    public $pns = null;
    /**
     * Create a new job instance.
     */
    public function __construct($type, $item, $recipients, $sendMode)
    {
        if (!in_array($sendMode, $this->sendModeOptions)) {
            throw new \Exception('Invalid send mode');
        }

        if (!$type) {
            throw new \Exception('Notification type not set');
        }

        if (!$item) {
            throw new \Exception('Notification item not set');
        }

        if (!$recipients && $sendMode !== 'all') {
            throw new \Exception('Notification recipients not set');
        }


        $this->recipients = $recipients;
        $this->sendMode = $sendMode;

        $notificationFactory = NotificationsFactory::getFactory($sendMode);
        $this->pns = new $notificationFactory($type, $item);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->pns->setQueryParameters($this->recipients)
            ->setQuery()
            ->process();
    }
}
