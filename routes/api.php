<?php

use App\Http\Controllers\PollController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (AJAX Endpoints)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Poll results (real-time updates)
    Route::get('/polls/{poll}/results', [PollController::class, 'getResults']);

    // Vote submission
    Route::post('/polls/{poll}/vote', [VoteController::class, 'store']);

    // Admin API routes
    Route::post('/admin/polls/{poll}/release-ip', [AdminController::class, 'releaseIp']);
    Route::get('/admin/polls/{poll}/ip-history', [AdminController::class, 'showIpHistory']);
});
