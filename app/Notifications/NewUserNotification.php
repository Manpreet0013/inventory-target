<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewUserNotification extends Notification
{
    use Queueable;

    public string $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "A new user '{$this->name}' has been created by Admin.",
        ];
    }
}
