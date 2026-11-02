<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use Illuminate\Support\Facades\App;

class MessageReceivedNotification extends Notification
{
    use Queueable;
    private $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {
        $locale = App::currentLocale();
        return (new ExpoMessage())
            ->to([$notifiable])
            ->title(__('messages.NEW_MESSAGE_RECEIVED_TITLE'))
            ->body($this->message[$locale == 'ar' ? 'message_ar' : 'message'])
            ->shouldBatch()
            ->channelId('message_received');
    }
}
