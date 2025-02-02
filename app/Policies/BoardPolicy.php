<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\BoardTemplate;
use App\Models\User;

class BoardPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.boards', 'web');
    }

    public function view(User $user, Board $board): bool
    {
        if (!$user->belongsToTeam($board->team)) {
            return false;
        }

        return $board->owner_id === $user->id || $user->hasPermissionTo('view.boards', 'web');
    }

    public function create(User $user, BoardTemplate $template): bool
    {
        if (!$user->belongsToTeam($template->team)) {
            return false;
        }
        return $user->hasPermissionTo('create.boards', 'web');
    }

    public function update(User $user, Board $board): bool
    {
        if (!$user->belongsToTeam($board->team)) {
            return false;
        }

        return $user->hasPermissionTo('edit.boards', 'web') ||
               $user->id === $board->owner_id;
    }

    public function delete(User $user, Board $board): bool
    {
        if (!$user->belongsToTeam($board->team)) {
            return false;
        }

        return $user->hasPermissionTo('delete.boards', 'web') ||
               $user->id === $board->owner_id;
    }

    public function lock(User $user, Board $board): bool
    {
        if (!$user->belongsToTeam($board->team)) {
            return false;
        }

        return $user->hasPermissionTo('lock.boards', 'web') ||
               $user->id === $board->owner_id;
    }
}
