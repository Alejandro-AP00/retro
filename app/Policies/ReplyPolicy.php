<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;

class ReplyPolicy
{
    public function delete(User $user, Reply $reply): bool
    {
        // User can delete if they are the reply owner or the board owner
        return $user->id === $reply->user_id ||
               $user->id === $reply->column->board->owner_id;
    }
}
