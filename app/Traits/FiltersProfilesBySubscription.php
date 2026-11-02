<?php
namespace App\Traits;

trait FiltersProfilesBySubscription
{
    public function filterBySubscription($profiles)
    {
        $this->subscribedProfiles = collect();
        $this->profiles = collect();

        if ($this->isFreePeriod()) {
            $this->subscribedProfiles = $profiles;
            $this->profiles = collect();
            return $this;
        }

        $profiles->each(function ($profile) {
            if ($profile->user->isSubscribed()) {
                $this->subscribedProfiles->push($profile);
            } else {
                $this->profiles->push($profile);
            }
        });

        return $this;
    }
}