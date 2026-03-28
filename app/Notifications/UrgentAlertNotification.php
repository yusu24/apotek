<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UrgentAlertNotification extends Notification
{
    use Queueable;

    private $type;
    private $message;
    private $details;

    public function __construct($type, $message, $details = [])
    {
        $this->type = $type; // 'piutang' or 'expiry'
        $this->message = $message;
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'details' => $this->details,
        ];
    }
}
