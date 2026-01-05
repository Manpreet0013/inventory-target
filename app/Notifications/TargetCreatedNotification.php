<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Target;

class TargetCreatedNotification extends Notification
{
    use Queueable;

    public $target;

    /**
     * Create a new notification instance.
     */
    public function __construct(Target $target)
    {
        $this->target = $target;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail']; // or ['database'] if you want to store in DB
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Product Target Created')
                    ->line('A new target has been created for product: ' . $this->target->product->name)
                    ->line('Target Type: ' . $this->target->target_type)
                    ->line('Target Value: ' . $this->target->target_value)
                    ->line('Start Date: ' . $this->target->start_date)
                    ->line('End Date: ' . $this->target->end_date)
                    ->line('Please check the dashboard for more details.');
    }

    /**
     * Get the array representation of the notification (for database channel).
     */
    public function toArray($notifiable)
    {
        return [
            'target_id' => $this->target->id,
            'product_name' => $this->target->product->name,
        ];
    }
}
