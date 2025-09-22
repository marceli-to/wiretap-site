<?php

namespace App\Actions;

use App\Models\Log;

class StoreLogAction
{
	public function execute(array $data): Log
	{
		$log = Log::create([
			'timestamp' => \Carbon\Carbon::parse($data['timestamp'])->setTimezone(config('app.timezone')),
			'level' => $data['level'],
			'message' => $data['message'],
			'context' => $data['context'] ?? null,
			'app_name' => $data['app']['name'] ?? null,
			'app_env' => $data['app']['env'] ?? null,
			'app_url' => $data['app']['url'] ?? null,
			'server_hostname' => $data['server']['hostname'] ?? null,
			'server_ip' => $data['server']['ip'] ?? null,
		]);

		return $log;
	}
}