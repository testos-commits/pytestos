<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

class AdminMessageCreatedNotification extends Notification
{
    use Queueable;
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }



    public function toExpoNotification($notifiable): ExpoMessage
    {   
        if(strlen($this->message) > 100){
            $this->message = substr($this->message, 0, 100) . '...';
        }

        return (new ExpoMessage())
            ->to([$notifiable])
            ->title(__('messages.ADMIN_MESSAGE_SENT'))
            ->body($this->message)
            ->channelId('admin_message_created')
            ->shouldBatch();

    }
}
