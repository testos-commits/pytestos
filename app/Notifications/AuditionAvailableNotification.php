<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use Illuminate\Support\Facades\App;

class AuditionAvailableNotification extends Notification
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
            ->title(__('messages.AUDITION_CREATED_TITLE'))
            ->body(__('messages.AUDITION_CREATED_BODY'))
            // ->title('Profile Boost Event')
            // ->body('Not many slots left for your profile boost event. Make sure to reserve now!')
            ->channelId('audition_created')
            ->shouldBatch();
    }
}
