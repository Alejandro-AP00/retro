<?php

namespace App\Http\Controllers;

use App\Models\BoardTemplate;
use App\Http\Requests\StoreBoardTemplateRequest;
use App\Http\Requests\UpdateBoardTemplateRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class BoardTemplateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Templates/Index', [
            'templates' => BoardTemplate::all()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Templates/Create');
    }

    public function store(StoreBoardTemplateRequest $request): RedirectResponse
    {
        BoardTemplate::create($request->validated());

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(BoardTemplate $template): Response
    {
        return Inertia::render('Templates/Edit', [
            'template' => $template
        ]);
    }

    public function update(UpdateBoardTemplateRequest $request, BoardTemplate $template): RedirectResponse
    {
        $template->update($request->validated());

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(BoardTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
