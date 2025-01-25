<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BoardTemplateController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ReplyVoteController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('templates', BoardTemplateController::class)
        ->except(['show']);

    Route::resource('boards', BoardController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::post('replies', [ReplyController::class, 'store'])
        ->name('replies.store');
    Route::delete('replies/{reply}', [ReplyController::class, 'destroy'])
        ->name('replies.destroy');

    Route::post('replies/{reply}/votes', [ReplyVoteController::class, 'store'])
        ->name('replies.votes.store');
});

require __DIR__.'/auth.php';
