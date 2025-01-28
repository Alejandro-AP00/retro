<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class TeamController extends Controller
{
    public function edit(Request $request, Team $team)
    {
        if ($request->user()->cannot('update', $team)) {
            abort(403);
        }

        return Inertia::render('Teams/Edit', [
            'team' => $team->load('users'),
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
}
