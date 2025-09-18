<?php

use App\Livewire\LogsDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', LogsDashboard::class);
Route::get('/logs', LogsDashboard::class)->name('logs.dashboard');
