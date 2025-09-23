<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
	use SoftDeletes;
  
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
		'fixed_at',
	];

	protected $casts = [
		'timestamp' => 'datetime',
		'context' => 'array',
		'fixed_at' => 'datetime',
	];
}
