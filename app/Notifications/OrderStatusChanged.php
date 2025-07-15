<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $oldStatus;
    public $newStatus;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Vault 64: Your Order Status Has Changed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We wanted to let you know that the status of your order #' . $this->order->order_number . ' has changed.')
            ->line('**Previous status:** ' . ucfirst($this->oldStatus))
            ->line('**New status:** ' . ucfirst($this->newStatus))
            ->line('You can view the details of your order and track its progress by clicking the button below.')
            ->action('View Order', url(route('orders.show', $this->order->id)))
            ->line('If you have any questions, feel free to reply to this email or contact our support team.')
            ->salutation('Thank you for shopping with Vault 64!')
            ->markdown('emails.orders.status_changed', [
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
            ]);
    }
} 