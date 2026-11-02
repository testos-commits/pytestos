<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

class ApplicationStatusChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {   
        return (new ExpoMessage())
            ->to([$notifiable])
            ->title(__('messages.APPLICATION_STATUS_CHANGED_TITLE'))
            ->body(__('messages.APPLICATION_STATUS_CHANGED_BODY'))
            ->shouldBatch()
            ->channelId('application_status_changed');
    }
}
