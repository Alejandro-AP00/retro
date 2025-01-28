<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BoardTemplateController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ReplyVoteController;
use App\Http\Controllers\CurrentTeamController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamInvitationController;
use App\Http\Controllers\TeamMemberController;

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

    Route::put('/current-team', [CurrentTeamController::class, 'update'])
        ->name('current-team.update');

    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');

    Route::post('/teams/{team}/members', [TeamInvitationController::class, 'store'])->name('team-invitations.store');
    Route::put('/teams/{team}/members/{user}', [TeamMemberController::class, 'update'])->name('team-members.update');
    Route::delete('/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy'])->name('team-members.destroy');
});

Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
    ->name('team-invitations.accept');

require __DIR__.'/auth.php';
