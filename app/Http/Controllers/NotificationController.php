<?php

namespace App\Http\Controllers;

use App\Services\NotificationsService;
use App\Models\Notification;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\NotificationResource;
use App\Services\MessageService;
use App\Services\AdminMessageService;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(NotificationsService::getProfileNotifications());
    }

    public function hasUnreadV2()
    {
        Log::channel('debug')->info('Fetching unread notifications');
        $notifications = NotificationsService::hasUnreadNotifications();
        $messageService = new MessageService();
        $adminMessageService = new AdminMessageService();

        $notifications['has_unread_audition_messages'] = $messageService->hasUnreadAuditionMessages();
        $notifications['has_unread_admin_messages'] = $adminMessageService->hasUnreadAdminMessages();

        return response()->json($notifications);
    }

    public function hasUnread()
    {

        return response()->json(NotificationsService::hasUnreadNotifications());
    }

    public function updateLastSeen()
    {
        NotificationsService::updateLastSeen();
        return response()->json();
    }
}
