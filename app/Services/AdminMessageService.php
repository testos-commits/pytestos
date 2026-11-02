<?php

namespace App\Services;

use App\Models\AdminMessage;
use App\Http\Resources\AdminMessageResource;
class AdminMessageService
{

    public static function updateReadStatus()
    {
        auth()->user()->profile->last_read_admin_message = now();

        return auth()->user()->profile->save();
    }

    public function hasUnreadAdminMessages() {
        $hasUnread = 0;

        $messages = AdminMessage::where('country_id', auth()->user()->profile->country_id)->orWhereNull('country_id')->get();


        if (auth()->user()->profile->last_read_admin_message) {
            $unreadMessages = $messages->where('created_at', '>', auth()->user()->profile->last_read_admin_message);
            $hasUnread = $unreadMessages->count();
        } else {
            $hasUnread = $messages->count();
        }

        return $hasUnread > 0;
    }


    public static function get()
    {   
        $hasUnread = 0;

        $messages = AdminMessage::where('country_id', auth()->user()->profile->country_id)->orWhereNull('country_id')->get();


        if (auth()->user()->profile->last_read_admin_message) {
            $unreadMessages = $messages->where('created_at', '>', auth()->user()->profile->last_read_admin_message);
            $hasUnread = $unreadMessages->count();
        } else {
            $hasUnread = $messages->count();
        }

        /** @todo add actual image logo */
        return [
            'messages' => AdminMessageResource::collection($messages),
            'unread' => $hasUnread,
            'title' => 'PY Team Messages',
            'image' => 'https://www.papayatech.com/wp-content/uploads/2021/06/papayatech-logo.png',
            'last_message_at' => $messages->last() ? $messages->last()->created_at->diffForHumans() : null
        ];
    }
}
