<?php

namespace App\Http\Controllers;

use App\Models\BoardTemplate;
use App\Http\Requests\StoreBoardTemplateRequest;
use App\Http\Requests\UpdateBoardTemplateRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BoardTemplateController extends Controller
{
    public function index(Request $request): Response
    {
        if ($request->user()->cannot('viewAny', BoardTemplate::class)) {
            abort(403);
        }

        return Inertia::render('Templates/Index', [
            'templates' => BoardTemplate::where('team_id', $request->user()->current_team_id)
                ->get()
        ]);
    }

    public function create(Request $request): Response
    {
        if ($request->user()->cannot('create', BoardTemplate::class)) {
            abort(403);
        }

        return Inertia::render('Templates/Create');
    }

    public function store(StoreBoardTemplateRequest $request): RedirectResponse
    {
        if ($request->user()->cannot('create', BoardTemplate::class)) {
            abort(403);
        }

        BoardTemplate::create([
            ...$request->validated(),
            'team_id' => $request->user()->current_team_id, // Add team association
        ]);

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(Request $request, BoardTemplate $template): Response
    {
        if ($request->user()->cannot('update', $template)) {
            abort(403);
        }

        return Inertia::render('Templates/Edit', [
            'template' => $template
        ]);
    }

    public function update(UpdateBoardTemplateRequest $request, BoardTemplate $template): RedirectResponse
    {
        if ($request->user()->cannot('update', $template)) {
            abort(403);
        }

        $template->update($request->validated());

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(Request $request, BoardTemplate $template): RedirectResponse
    {
        if ($request->user()->cannot('delete', $template)) {
            abort(403);
        }

        $template->delete();

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
