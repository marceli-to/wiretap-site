<?php

namespace App\Notifications;

use App\Channels\PusherBeamsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WebhookNotification extends Notification
{
    use Queueable;

    private $logData;
    private $title;
    private $priority;

    public function __construct(array $logData)
    {
        $this->logData = $logData;
        $this->title = $this->generateTitle();
        $this->priority = $this->determinePriority();
    }

    public function via(object $notifiable): array
    {
        return [PusherBeamsChannel::class, 'database'];
    }

    public function toPusherBeams($notifiable)
    {
        return [
            'web' => [
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->generateDetailedBody(),
                    'icon' => url('/favicon.ico'),
                    'badge' => url('/favicon.ico'),
                    'tag' => 'log-' . ($this->logData['id'] ?? 'unknown'), // Group similar notifications
                    'requireInteraction' => $this->priority === 'high', // Keep critical alerts visible
                    'data' => [
                        'url' => route('logs.dashboard'),
                        'log_id' => $this->logData['id'] ?? null,
                        'level' => $this->logData['level'] ?? 'unknown',
                        'app_name' => $this->logData['app']['name'] ?? 'Unknown App',
                        'server' => $this->logData['server']['hostname'] ?? 'Unknown Server',
                        'timestamp' => $this->logData['timestamp'] ?? now()->toISOString(),
                        'priority' => $this->priority
                    ],
                    'actions' => [
                        [
                            'action' => 'view-logs',
                            'title' => 'View All Logs',
                            'icon' => url('/favicon.ico')
                        ],
                        [
                            'action' => 'dismiss',
                            'title' => 'Dismiss',
                            'icon' => url('/favicon.ico')
                        ]
                    ]
                ]
            ]
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->generateBody(),
            'log_data' => $this->logData,
            'priority' => $this->priority,
            'timestamp' => now()->toISOString()
        ];
    }

    private function generateTitle(): string
    {
        $level = $this->logData['level'] ?? 'info';
        $source = $this->logData['source'] ?? 'System';

        return match(strtolower($level)) {
            'error', 'critical', 'emergency' => "{$source} Error",
            'warning' => "{$source} Warning",
            'info' => "{$source} Update",
            'debug' => "{$source} Debug",
            default => "{$source} Log"
        };
    }

    private function generateBody(): string
    {
        $message = $this->logData['message'] ?? 'New log entry received';

        // Truncate long messages for notification
        if (strlen($message) > 100) {
            $message = substr($message, 0, 97) . '...';
        }

        return $message;
    }

    private function generateDetailedBody(): string
    {
        $parts = [];

        // 1. The message (truncated)
        $message = $this->logData['message'] ?? 'New log entry received';
        if (strlen($message) > 120) {
            $message = substr($message, 0, 117) . '...';
        }
        $parts[] = $message;

        // 2. The application
        if (isset($this->logData['app']['name'])) {
            $parts[] = "📱 " . $this->logData['app']['name'];
        }

        // 3. The environment
        if (isset($this->logData['app']['env'])) {
            $envEmoji = match(strtolower($this->logData['app']['env'])) {
                'production' => '🔴',
                'staging' => '🟡',
                'development', 'local' => '🟢',
                default => '⚪'
            };
            $parts[] = $envEmoji . " " . ucfirst($this->logData['app']['env']);
        }

        return implode("\n", $parts);
    }

    private function determinePriority(): string
    {
        $level = strtolower($this->logData['level'] ?? 'info');

        return match($level) {
            'emergency', 'critical', 'error' => 'high',
            'warning' => 'medium',
            default => 'low'
        };
    }
}
