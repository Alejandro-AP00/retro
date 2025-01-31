<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrentTeamController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $team = Team::findOrFail($validated['team_id']);

        if (!$request->user()->belongsToTeam($team)) {
            return response()->json(['message' => 'You do not belong to this team.'], 403);
        }

        $request->user()->switchTeam($team);

        return redirect()->route('dashboard');
    }
}
