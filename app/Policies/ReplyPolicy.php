<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\Column;
use App\Models\User;

class ReplyPolicy
{
    public function create(User $user, Column $column): bool
    {
        // Check if user belongs to the board's team
        return $user->belongsToTeam($column->board->team) &&
               !$column->board->isLocked();
    }

    public function vote(User $user, Reply $reply): bool
    {
        return $user->belongsToTeam($reply->column->board->team) &&
               !$reply->column->board->isLocked();
    }

    public function delete(User $user, Reply $reply): bool
    {
        if (!$user->belongsToTeam($reply->column->board->team)) {
            return false;
        }

        // Check team-specific permissions
        return $user->id === $reply->user_id || // Reply owner
               $user->id === $reply->column->board->owner_id || // Board owner
               $user->hasPermissionTo('delete boards'); // Team admin
    }
}
