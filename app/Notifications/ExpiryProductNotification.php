<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ExpiryProductNotification extends Notification
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'expiry_date' => $this->product->expiry_date,
            'message' => 'Product is expiring within 6 months'
        ];
    }
}
