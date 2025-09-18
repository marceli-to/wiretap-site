<?php

namespace App\Actions;

use App\Models\Log;
use App\Models\User;
use App\Notifications\WebhookNotification;
use Illuminate\Support\Facades\Notification;

class StoreLogAction
{
	public function execute(array $data): Log
	{
		$log = Log::create([
			'timestamp' => $data['timestamp'],
			'level' => $data['level'],
			'message' => $data['message'],
			'context' => $data['context'] ?? null,
			'app_name' => $data['app']['name'] ?? null,
			'app_env' => $data['app']['env'] ?? null,
			'app_url' => $data['app']['url'] ?? null,
			'server_hostname' => $data['server']['hostname'] ?? null,
			'server_ip' => $data['server']['ip'] ?? null,
		]);

		// Send push notifications for important log levels
		$this->sendNotificationsIfNeeded($log, $data);

		return $log;
	}

	private function sendNotificationsIfNeeded(Log $log, array $data): void
	{
		$level = strtolower($data['level'] ?? 'info');

		// Define which log levels should trigger notifications
		$notifiableLevels = ['error', 'critical', 'emergency', 'warning'];

		if (in_array($level, $notifiableLevels)) {
			// Get users who should receive notifications
			$users = $this->getUsersToNotify($level, $data);

			if ($users->isNotEmpty()) {
				// Prepare notification data
				$notificationData = array_merge($data, [
					'id' => $log->id,
					'source' => $data['app']['name'] ?? 'Unknown App',
					'level' => $level
				]);

				// Send notifications
				Notification::send($users, new WebhookNotification($notificationData));
			}
		}
	}

	private function getUsersToNotify(string $level, array $data): \Illuminate\Database\Eloquent\Collection
	{
		// For now, notify all users with push subscriptions
		// You can customize this logic based on your needs:
		// - User preferences (notification settings)
		// - User roles/permissions
		// - App-specific subscriptions
		// - Time-based rules (working hours, etc.)

		return User::whereHas('pushSubscriptions')->get();

		// Example of more advanced targeting:
		// return User::whereHas('pushSubscriptions')
		//     ->when($level === 'critical', function($query) {
		//         // Only notify admins for critical errors
		//         return $query->where('role', 'admin');
		//     })
		//     ->when($level === 'warning', function($query) use ($data) {
		//         // Only notify users subscribed to this specific app
		//         return $query->whereJsonContains('notification_preferences->apps', $data['app']['name']);
		//     })
		//     ->get();
	}
}