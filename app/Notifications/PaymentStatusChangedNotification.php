<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

class PaymentStatusChangedNotification extends Notification
{
    use Queueable;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {   
        $title = $this->status === 'ready_for_pickup' ? __('messages.PAYMENT_READY_FOR_PICKUP') : __('messages.PAYMENT_SETTLED');
        $body = $this->status === 'ready_for_pickup' ? __('messages.PAYMENT_READY_FOR_PICKUP') : __('messages.PAYMENT_SETTLED');

        return (new ExpoMessage())
            ->to([$notifiable])
            ->title($title)
            ->body($body)
            ->shouldBatch()
            ->channelId('payment_status_changed');
    }
}
