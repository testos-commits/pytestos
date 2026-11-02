<?php

namespace App\Services;

use App\Traits\Notifications\HasNotificationList;
use App\Traits\Notifications\SendsPushNotifications;
use App\Traits\Notifications\DebugNotifications;
use App\Traits\Notifications\PreparesNotificationsMessages;
use App\Interfaces\AppNotifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class NotificationsService implements AppNotifications
{
    use HasNotificationList,
        SendsPushNotifications,
        PreparesNotificationsMessages,
        DebugNotifications;

    public $message = [],
        $redirects = [],
        $languages = [],
        $data = [],
        $notificationsListData = [],
        $pushNotificationsData = [];

    public Builder $query;
    public mixed $parameter;
    public $chunkSize = 90;
    public $type = '';
    public $profiles = null;
    public $subscribedProfiles = null;
    public $item;
    public $chunkIndex = 0;
    public $chunkDelay = 10;

    public function __construct($type, $item)
    {
        if (!in_array($type, array_keys(config('notification-messages')))) {
            throw new \Exception('Invalid notification type');
        }

        $this->redirects = config('notification-redirects.' . $type);
        $this->languages = config('app.supported_locales');
        $this->item = $item;
        $this->type = $type;

        //needs to be last after the $item & $type properties are set
        $this->message = $this->getFormattedMessage(
            config('notification-messages.' . $type)
        );
    }

    public function prepareNotifications()
    {
        $this->resetPushNotificationsData();
        $this->resetNotificationListData();
        // prepare the image to be shown in the notification list

        $image = $this->getNotificationImage();

        $this->profiles->each(function ($profile) use ($image) {
            if ($this->shouldAddToList()) {
                $this->setNotificationListData($profile, $image);
            }

            $this->setPushNotificationsData($profile);
        });


        if (is_null($this->subscribedProfiles) || count($this->subscribedProfiles) === 0) {
            return;
        }

        foreach ($this->subscribedProfiles as $profile) {
            if ($this->shouldAddToList()) {
                $this->setNotificationListData($profile, $image);
            }
            $this->setPushNotificationsData($profile);
        }


        Log::channel('notifications')->info('Preparing notifications in Notification Service', [
            'total' => sizeof($this->notificationsListData),
            'time' => now()->toDateTimeString(),
            'duplicates' => $this->registeredDuplicateNotificationInList(),
            'duplicates_count' => count($this->registeredDuplicateNotificationInList()),
        ]);
    }

    public function send()
    {
        if (is_null($this->profiles)) {
            $this->profiles = collect([]);
        }

        if (is_null($this->subscribedProfiles)) {
            $this->subscribedProfiles = collect([]);
        }

        if ($this->profiles->isEmpty() && $this->subscribedProfiles->isEmpty()) {
            return;
        }

        // Prepares for both subscribed and not subscribed users
        $this->prepareNotifications();

        // Saves the notifications to show in the list on the app
        if ($this->shouldAddToList()) {
            $this->createNotificationsList();
        }

        $this->sendPushNotifications();

        return $this;
    }
}
