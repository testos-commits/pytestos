<?php

namespace App\Traits\Notifications;

trait DebugNotifications
{
    public function registeredDuplicateNotificationInList()
    {
        $duplicates = [];

        foreach ($this->notificationsListData as $item) {
            if (isset($duplicates[$item['user_profile_id']])) {
                $duplicates[$item['user_profile_id']]++;
            } else {
                $duplicates[$item['user_profile_id']] = 1;
            }
        }

        // Filter out profiles with more than 1 notification
        $duplicates = array_filter($duplicates, function ($count) {
            return $count > 1;
        });
        
        return $duplicates;
    }
}
