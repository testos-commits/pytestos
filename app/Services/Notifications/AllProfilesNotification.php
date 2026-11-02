<?php

namespace App\Services\Notifications;

use App\Services\NotificationsService;
use App\Models\Profile;
use App\Interfaces\ProcessableNotification;
use Illuminate\Support\Facades\Log; 

class AllProfilesNotification extends NotificationsService implements ProcessableNotification
{
    public $chunkDelay = 100;

    public function setQueryParameters(mixed $parameter)
    {
        Log::channel('notifications')->info('AllProfilesNotification setQueryParameters', ['parameter' => $parameter]);
        if ($parameter) {
            $this->parameter = $parameter;
        }
        return $this;
    }

    public function setQuery()
    {
        $this->query = Profile::hasExpoToken(); 
        return $this;
    }

    public function process()
    {
        Log::channel('notifications')->info('AllProfilesNotification processing', [
            'total' => $this->query->count(),
            'time' => now()->toDateTimeString(),
        ]);

        $this->query->chunk($this->chunkSize, function ($profiles) {
            $this->profiles = collect();
            $this->chunkIndex++;
            $this->profiles = $profiles;
            $this->send();
        });

        return $this;
    }
}