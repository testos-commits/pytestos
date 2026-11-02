<?php

namespace App\Services\Notifications;

use App\Services\NotificationsService;
use App\Models\Profile;
use App\Interfaces\ProcessableNotification;
use Illuminate\Support\Facades\Log; 

class CountryNotification extends NotificationsService implements ProcessableNotification
{
    public $chunkDelay = 100;

    public function setQueryParameters(mixed $parameter)
    {
        Log::channel('notifications')->info('CountryNotification setQueryParameters', ['parameter' => $parameter]);
        $this->parameter = $parameter;
        return $this;
    }

    public function setQuery()
    {
        $this->query = Profile::hasExpoToken()
            ->whereHas('user', function ($query) {
                $query->whereNotNull('email_verified_at');
            });

        if (is_array($this->parameter)) {
            $this->query->whereIn('country_id', $this->parameter);
        } else {
            $this->query->where('country_id', $this->parameter);
        }
        return $this;
    }

    public function process()
    {
        Log::channel('notifications')->info('CountryNotification processing', [
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
