<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
	protected $fillable = [
		'timestamp',
		'level',
		'message',
		'context',
		'app_name',
		'app_env',
		'app_url',
		'server_hostname',
		'server_ip',
	];

	protected $casts = [
		'timestamp' => 'datetime',
		'context' => 'array',
	];
}
