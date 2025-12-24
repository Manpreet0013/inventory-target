<?php

namespace App\Notifications;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SaleAddedNotification extends Notification
{
    use Queueable;

    public Sale $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $productName = $this->sale->target->product->name ?? 'Unknown Product';
        $executiveName = $this->sale->executive->name ?? 'Unknown Executive';

        return [
            'message' => "A new sale has been added for '{$productName}' by {$executiveName}.",
            'sale_id' => $this->sale->id,
        ];
    }

}
