<?php

namespace App\Notifications;

use App\Channels\PusherBeamsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TestPushNotification extends Notification
{
    use Queueable;

    private $title;
    private $body;

    public function __construct($title = 'Test Notification', $body = 'This is a test push notification!')
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function via(object $notifiable): array
    {
        return [PusherBeamsChannel::class];
    }

    public function toPusherBeams($notifiable)
    {
        return [
            'web' => [
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->body,
                    'icon' => url('/favicon.ico'),
                    'badge' => url('/favicon.ico'),
                ]
            ]
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}
