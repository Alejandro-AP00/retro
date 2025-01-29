<?php

use App\Models\Board;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('board.{boardId}', function (User $user, int $boardId) {
    if (Board::find($boardId)->team_id === $user->current_team_id){
        return ['id' => $user->id, 'name' => $user->name];
    };
});
