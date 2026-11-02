<?php

namespace App\Traits\Notifications;

use Illuminate\Support\Carbon;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\Log;

trait HasNotificationList
{
    public static function hasUnreadNotifications()
    {
        $profile = auth()->user()->profile;
        $lastRead = $profile->last_read_notifications ? Carbon::parse($profile->last_read_notifications) : null;

        $notifications = $profile->notifications;

        if (!$lastRead && $notifications->count() > 0) {
            return [
                'has_unread_notifications' => true,
            ];
        } elseif (!$lastRead && $notifications->count() === 0) {
            return [
                'has_unread_notifications' => false,
            ];
        }

        $unreadNotifications = $notifications->where('created_at', '>', $lastRead);

        return [
            'has_unread_notifications' => $unreadNotifications->count() > 0,
        ];
    }

    public static function updateLastSeen() {
        $profile = auth()->user()->profile;
        $profile->last_read_notifications = Carbon::now();
        $profile->save();
    }

    public static function getProfileNotifications(): array
    {
        $profile = auth()->user()->profile;
        $lastRead = $profile->last_read_notifications ? Carbon::parse($profile->last_read_notifications) : null;
        $hasUnreadNotifications = false;

        $perPage = 10;
        $entries = Notification::where('user_profile_id', $profile->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Group entries by date
        foreach ($entries as $entry) {
            $entry->seen = $lastRead ? $lastRead->gte(
                Carbon::parse($entry->created_at)
            ) : false;

            if (!$entry->seen) {
                $hasUnreadNotifications = true;
            }
        }

        return [
            'data' =>  NotificationResource::collection($entries),
            'has_unread_notifications' => $hasUnreadNotifications,
            'nextPage' => $entries->hasMorePages() ? $entries->currentPage() + 1 : null,
        ];
    }

    public function getNotificationImage()
    {
        switch ($this->type) {
            case 'PAYMENT_READY_FOR_PICKUP':
            case 'PAYMENT_STATUS_CHANGED':
            case 'PAYMENT_IN_PROCESS':
            case 'PAYMENT_SETTLED':
                return null;

            default:
                return  config('aws.aws-urls.admin_base_url') . $this->item->image;
        }
    }

    public function extractMessageBody()
    {
        $bodies = [];

        foreach ($this->languages as $language) {
            if (isset($this->message[$language])) {
                $bodies[$language] = $this->message[$language]['body'];
            }
        }

        return $bodies;
    }

    public function resetNotificationListData() {
        $this->notificationsListData = [];
    }

    public function setNotificationListData($profile, $notificationImage)
    {
        $this->notificationsListData[] = [
            'user_profile_id' => $profile->id,
            'title' => json_encode($this->extractMessageBody()),
            'data' => json_encode([
                'type' => $this->type,
                'id' => $this->item->id,
                'image' => $notificationImage,
            ]),
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now(),
        ];
    }

    public function createNotificationsList()
    {
        Notification::insert($this->notificationsListData);
    }

    public function shouldAddToList()
    {
        return $this->type !== 'NEW_MESSAGE' && $this->type !== 'NEW_ADMIN_MESSAGE';
    }
}
