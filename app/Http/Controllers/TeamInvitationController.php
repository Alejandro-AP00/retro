<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteTeamMemberRequest;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class TeamInvitationController extends Controller
{
    public function store(InviteTeamMemberRequest $request, Team $team)
    {
        if ($request->user()->cannot('invite', $team)) {
            abort(403);
        }

        $invitation = $team->invitations()->create([
            'email' => $request->email,
            'role_id' => Role::where('name', $request->role)->first()->id,
            'token' => Str::random(32),
            'expires_at' => now()->addDays(7),
        ]);

        Notification::route('mail', $invitation->email)
                ->notify(new TeamInvitationNotification($invitation));

        return back()->with('success', 'Invitation sent successfully.');
    }

    public function accept(Request $request, TeamInvitation $invitation)
    {
        if ($invitation->isExpired()) {
            return redirect()->route('login')
                ->with('error', 'This invitation has expired.');
        }

        if ($request->user()) {
            $invitation->team->users()->attach($request->user());
            $request->user()->switchTeam($invitation->team);
            $request->user()->syncRoles([$invitation->role]);
            $invitation->delete();

            return redirect()->route('dashboard')
                ->with('success', 'You have joined the team.');
        }

        return redirect()->route('register', ['invitation' => $invitation->token]);
    }
}
