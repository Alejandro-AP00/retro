<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Models\TeamInvitation;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        $invitation = null;
        if ($request->has('invitation')) {
            $invitation = TeamInvitation::where('token', $request->invitation)
                ->whereNull('registered_at')
                ->firstOrFail();

            if ($invitation->isExpired()) {
                abort(403, 'This invitation has expired.');
            }
        }

        return Inertia::render('Auth/Register', [
            'invitation' => $invitation ? [
                'email' => $invitation->email,
                'team' => $invitation->team->name,
            ] : null,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invitation_token' => 'nullable|string|exists:team_invitations,token',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->invitation_token) {
            $invitation = TeamInvitation::where('token', $request->invitation_token)
                ->whereNull('registered_at')
                ->firstOrFail();

            if (!$invitation->isExpired() && $invitation->email === $user->email) {
                $user->teams()->attach($invitation->team_id);
                $user->switchTeam($invitation->team);
                $user->assignRole($invitation->role);

                $invitation->update(['registered_at' => now()]);
            }
        } else{
            $team = Team::create([
                'name' => $request->name . "'s Team"
            ]);
            $user->teams()->attach($team);
            $user->switchTeam($team);
            $user->assignRole('admin');

        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
