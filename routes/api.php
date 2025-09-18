<?php

use App\Http\Controllers\WebhookController;
use App\Http\Middleware\WebhookAuth;
use Illuminate\Support\Facades\Route;

// Webhook endpoint with authentication
Route::post('/webhook/logs', [WebhookController::class, 'store'])
    ->middleware(WebhookAuth::class);