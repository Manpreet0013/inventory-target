<?php

namespace App\Notifications;

use App\Models\Target;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TargetAssignedNotification extends Notification
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
            'message' => "You have been assigned a new target for '{$this->target->product->name}'.",
            'target_id' => $this->target->id,
        ];
    }
}
