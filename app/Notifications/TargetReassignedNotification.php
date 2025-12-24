<?php

namespace App\Notifications;

use App\Models\Target;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TargetReassignedNotification extends Notification
{
    use Queueable;

    public Target $target;

    public function __construct(Target $target)
    {
        $this->target = $target;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Target '{$this->target->product->name}' has been reassigned to you.",
            'target_id' => $this->target->id,
        ];
    }
}
