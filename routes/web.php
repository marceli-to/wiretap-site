<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PusherBeamsController;
use App\Livewire\LogsDashboard;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// Authenticated routes
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Email verification routes
Route::get('/verify-email', [VerifyEmailController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// Push notification routes
Route::match(['get', 'post'], '/api/pusher/beams-auth', [PusherBeamsController::class, 'beamsAuth'])
    ->middleware(['auth', 'verified']);
Route::post('/api/pusher/store-subscription', [PusherBeamsController::class, 'storeSubscription'])
    ->middleware(['auth', 'verified']);
Route::get('/api/pusher/check-subscription', [PusherBeamsController::class, 'checkSubscription'])
    ->middleware(['auth', 'verified']);

// Protected application routes
Route::get('/', LogsDashboard::class)->middleware(['auth', 'verified']);
Route::get('/logs', LogsDashboard::class)->name('logs.dashboard')->middleware(['auth', 'verified']);
