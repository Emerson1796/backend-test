<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\StatsController;

// CRUD de Redirects
Route::apiResource('redirects', RedirectController::class);

// Estatísticas de Acesso para um Redirect específico
Route::get('redirects/{redirect}/stats', [StatsController::class, 'showStats'])->name('redirects.stats');

// Logs de Acesso para um Redirect específico
Route::get('redirects/{redirect}/logs', [StatsController::class, 'showLogs'])->name('redirects.logs');

Route::get('/r/{code}', [RedirectController::class, 'redirectToDestination'])->name('redirect.to.destination');
