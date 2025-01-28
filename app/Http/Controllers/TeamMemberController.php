<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTeamMemberRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function update(UpdateTeamMemberRequest $request, Team $team, User $user)
    {
        if ($request->user()->cannot('updateMember', [$team, $user])) {
            abort(403);
        }

        $user->syncRoles($request->role);

        return back()->with('success', 'Member role updated successfully.');
    }

    public function destroy(Request $request, Team $team, User $user)
    {
        if ($request->user()->cannot('removeMember', [$team, $user])) {
            abort(403);
        }

        $user->teams()->detach($team);

        return back()->with('success', 'Member removed successfully.');
    }
}
