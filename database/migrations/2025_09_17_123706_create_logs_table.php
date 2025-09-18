<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('logs', function (Blueprint $table) {
			$table->id();
			$table->timestamp('timestamp');
			$table->string('level');
			$table->text('message');
			$table->json('context')->nullable();
			$table->string('app_name')->nullable();
			$table->string('app_env')->nullable();
			$table->string('app_url')->nullable();
			$table->string('server_hostname')->nullable();
			$table->string('server_ip')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('logs');
	}
};
