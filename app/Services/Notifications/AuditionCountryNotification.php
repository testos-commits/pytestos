<?php

namespace App\Services\Notifications;

use App\Services\NotificationsService;
use App\Models\Profile;
use App\Traits\FiltersProfilesByAuditionCriteria;
use App\Traits\FiltersProfilesBySubscription;
use App\Traits\RequiresSubscription;

use Illuminate\Support\Facades\Log;
use App\Interfaces\ProcessableNotification;


class AuditionCountryNotification extends NotificationsService implements ProcessableNotification
{
    use FiltersProfilesByAuditionCriteria,
        FiltersProfilesBySubscription,
        RequiresSubscription;


    public function setQueryParameters(mixed $parameter)
    {
        Log::channel('notifications')->info('AuditionCountry setQueryParameters', ['parameter' => $parameter]);
        $this->parameter = $parameter;
        return $this;
    }

    public function setQuery()
    {
        $this->query = Profile::hasExpoToken();

        if (is_array($this->parameter)) {
            $this->query->whereIn('country_id', $this->parameter);
        } else {
            $this->query->where('country_id', $this->parameter);
        }

        return $this;
    }

    public function process()
    {
        Log::channel('notifications')->info('AuditionCountryNotification processing', [
            'total' => $this->query->count(),
            'time' => now()->toDateTimeString(),
        ]);

        $totalSentTo = 0;

        $this->query->chunk($this->chunkSize, function ($profiles) use (&$totalSentTo) {
            $this->chunkIndex++;
            $this->filterBySubscription($profiles)
                ->filterProfilesByAuditionCriteria($this->item)
                ->send();
            
            $totalSentTo += $this->subscribedProfiles->count();
        });

        Log::channel('notifications')->info('AuditionCountryNotification processed', [
            'total' => $this->query->count(),
            'sent_to_after_filtering' => $totalSentTo,
            'time' => now()->toDateTimeString(),
        ]);

        return $this;
    }
}
