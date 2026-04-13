<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoggingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/ai/handle', [DashboardController::class, 'aiRequestHandler'])->name('ai.handle');
Route::get('/history', [LoggingController::class, 'history'])->name('history');
