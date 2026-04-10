<?php

use App\Http\Controllers\DashboardController;
use App\Models\Logging;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/ai/handle', [DashboardController::class, 'aiRequestHandler'])->name('ai.handle');

Route::get('/history', function () {
	$logs = Logging::query()
		->latest('id')
		->limit(100)
		->get()
		->map(fn (Logging $log) => [
			'id' => $log->id,
			'created_at' => optional($log->created_at)->format('Y-m-d H:i:s'),
			'ai_module_used' => $log->ai_module_used,
			'prompt' => $log->prompt,
			'total_tokens_used' => $log->total_tokens_used,
			'response' => $log->response,
		])
		->values();

	return Inertia::render('History', [
		'logs' => $logs,
	]);
})->name('history');
