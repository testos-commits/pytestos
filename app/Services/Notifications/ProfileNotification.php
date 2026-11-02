<?php

namespace App\Services\Notifications;

use App\Services\NotificationsService;
use App\Models\Profile;
use App\Interfaces\ProcessableNotification;
use Illuminate\Support\Facades\Log;

class ProfileNotification extends NotificationsService implements ProcessableNotification
{
    public function setQueryParameters(mixed $parameter)
    {
        Log::channel('notifications')->info('ProfileNotification setQueryParameters', ['parameter' => $parameter]);
        $this->parameter = $parameter;
        return $this;
    }

    public function setQuery()
    {
        $this->query = Profile::hasExpoToken();

        if (is_array($this->parameter)) {
            $this->query->whereIn('id', $this->parameter);
        } else {
            $this->query->where('id', $this->parameter);
        }
        return $this;
    }

    public function process()
    {
        Log::channel('notifications')->info('ProfileNotification processing', [
            'total' => $this->query->count(),
            'time' => now()->toDateTimeString(),
        ]);
        $this->query->chunk($this->chunkSize, function ($profiles) {
            $this->chunkIndex++;
            $this->profiles = $profiles;
            $this->send();
        });

        return $this;
    }
}
