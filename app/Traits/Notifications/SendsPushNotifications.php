<?php

namespace App\Traits\Notifications;

use App\Jobs\SendExpoNotification;
use Illuminate\Support\Facades\Log;
use App\Services\ExpoNotificationService;

trait SendsPushNotifications
{
    public function resetPushNotificationsData()
    {
        $this->pushNotificationsData = [];
    }

    public function setPushNotificationsData($profile)
    {
        $user = $profile->user;

        if ($user->expoToken) {
            $this->pushNotificationsData[] = [
                'to' => $user->expoToken->value,
                'sound' => 'default',
                'body' =>  $profile->first_name . ' ' . $profile->last_name . ': ' . $this->message[$user->app_language]['body'],
                'title' => $this->message[$user->app_language]['title'],
                'data' => [
                    'id' => $profile->id, // SHOULD ACTUALLY BE THE NOTIFICATION ID
                    'image' => $this->getNotificationImage(),
                    'seen' => false,
                    'created_at' => now()->toDateTimeString(),
                    'profile_id' => $profile->id,
                    'title' => $this->message[$user->app_language]['body'],
                    'data' => [
                        'id' => $this->item->id,
                        'header_title' => $this->getAppHeaderTitle(),
                        'type' => $this->type,
                        'profile_id' => $profile->id,
                        'image' => $this->getNotificationImage(),
                    ]
                ]
            ];
        }
    }

    public function getAppHeaderTitle()
    {
        switch ($this->type) {
            case 'NEW_AUDITION':
            case 'NEW_MESSAGE':
                return $this->item->title;

            default:
                return null;
        }
    }

    public function sendPushNotifications()
    {   

        SendExpoNotification::dispatch($this->pushNotificationsData)->onQueue('notifications')
            ->delay($this->chunkIndex * $this->chunkDelay);
    }
}
