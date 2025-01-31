<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use App\Models\User;

class TeamController extends Controller
{
    public function edit(Request $request, Team $team)
    {
        if ($request->user()->cannot('update', $team)) {
            abort(403);
        }

        return Inertia::render('Teams/Edit', [
            'team' => $team->load(['users.roles']),
            'availableRoles' => Role::all(['id', 'name']),
        ]);
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        if ($request->user()->cannot('update', $team)) {
            abort(403);
        }

        $team->update($request->validated());

        return redirect()->route('teams.edit', $team)
            ->with('success', 'Team updated successfully.');
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Team::class)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $team = Team::create([
            'name' => $validated['name']
        ]);

        // Add the creator as admin
        $request->user()->teams()->attach($team);
        $request->user()->assignRole('admin');
        $request->user()->refresh()->switchTeam($team);

        return redirect()->route('dashboard')->with('success', 'Team created successfully.');
    }
}
