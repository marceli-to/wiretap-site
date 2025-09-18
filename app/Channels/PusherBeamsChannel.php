<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Pusher\PushNotifications\PushNotifications;

class PusherBeamsChannel
{
    protected $beamsClient;

    public function __construct()
    {
        $this->beamsClient = new PushNotifications([
            'instanceId' => config('services.pusher.beams_instance_id'),
            'secretKey' => config('services.pusher.beams_secret_key'),
        ]);
    }

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toPusherBeams($notifiable);

        return $this->beamsClient->publishToUsers(
            [(string) $notifiable->getKey()], // Convert to string
            $message
        );
    }
}