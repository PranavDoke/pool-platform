<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes (no authentication required)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Polls
    Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
    Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');
    Route::post('/polls', [PollController::class, 'store'])->name('polls.store');
    Route::get('/polls/{poll}', [PollController::class, 'show'])->name('polls.show');
    Route::post('/polls/{poll}/toggle', [PollController::class, 'toggleStatus'])->name('polls.toggle');

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/polls/{poll}/voters', [AdminController::class, 'showPollVoters'])->name('admin.poll.voters');
    });
});
