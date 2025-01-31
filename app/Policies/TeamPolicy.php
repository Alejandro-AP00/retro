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
        return $user->hasRole('admin', 'web');
    }

    public function invite(User $user, Team $team): bool
    {
        return $user->hasRole('admin', 'web');
    }

    public function updateMember(User $user, Team $team, User $targetUser): bool
    {
        if ($targetUser->id === $team->owner_id) {
            return false;
        }

        return $user->hasRole('admin', 'web');
    }

    public function removeMember(User $user, Team $team, User $targetUser): bool
    {
        if ($targetUser->id === $team->owner_id) {
            return false;
        }

        return $user->hasRole('admin', 'web') || $user->id === $targetUser->id;
    }
}
