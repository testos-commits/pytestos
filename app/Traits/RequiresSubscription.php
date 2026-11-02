<?php

namespace App\Traits;

use App\Models\UserSubscription;
use Carbon\Carbon;

trait RequiresSubscription
{
    public function hasAccessToPremiumFeatures($userId)
    {
        return $this->userSubscribed($userId) || $this->isFreePeriod();
    }

    public function isFreePeriod()
    {
        $expiryDate = config('free-subscription-period.expiry_date');
        $today = Carbon::today();

        return $today->lessThan(Carbon::parse($expiryDate));
    }

    public function userSubscribed($userId)
    {
        return UserSubscription::where('user_id', $userId)->valid()->exists();
    }
}
