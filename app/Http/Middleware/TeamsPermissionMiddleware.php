<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeamsPermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            setPermissionsTeamId($user->current_team_id);
            $user->unsetRelation('roles')->unsetRelation('permissions');
        }

        return $next($request);
    }
}
