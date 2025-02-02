<?php

namespace App\Policies;

use App\Models\BoardTemplate;
use App\Models\User;

class BoardTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.templates', 'web');;
    }

    public function view(User $user, BoardTemplate $template): bool
    {
        if (!$user->belongsToTeam($template->team)) {
            return false;
        }

        return $user->hasPermissionTo('view.templates', 'web');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create.templates', 'web');
    }

    public function update(User $user, BoardTemplate $template): bool
    {
        if (!$user->belongsToTeam($template->team)) {
            return false;
        }

        return $user->hasPermissionTo('edit.templates', 'web');
    }

    public function delete(User $user, BoardTemplate $template): bool
    {
        if (!$user->belongsToTeam($template->team)) {
            return false;
        }

        return $user->hasPermissionTo('delete.templates', 'web');
    }
}
