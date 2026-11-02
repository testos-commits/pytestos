<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

class NewsCreatedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {   
        return (new ExpoMessage())
            ->to([$notifiable])
            ->title(__('messages.NEWS_CREATED_TITLE'))
            ->body(__('messages.NEWS_CREATED_BODY'))
            ->shouldBatch()
            ->channelId('news_created');
    }
}
