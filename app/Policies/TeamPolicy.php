<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Team $team): bool
    {
        if (!$user->belongsToTeam($team)) {
            return false;
        }

        return $user->hasPermissionTo('edit.teams', 'web');
    }

    public function invite(User $user, Team $team): bool
    {
        if (!$user->belongsToTeam($team)) {
            return false;
        }

        return $user->hasPermissionTo('invite.members', 'web');
    }

    public function updateMember(User $user, Team $team, User $targetUser): bool
    {
        if (!$user->belongsToTeam($team)) {
            return false;
        }

        return $user->hasPermissionTo('update.members', 'web');
    }

    public function removeMember(User $user, Team $team, User $targetUser): bool
    {
        if (!$user->belongsToTeam($team)) {
            return false;
        }

        return $user->hasPermissionTo('remove.members', 'web');
    }
}
