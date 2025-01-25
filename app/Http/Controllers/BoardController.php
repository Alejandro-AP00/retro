<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardTemplate;
use App\Models\Column;
use App\Http\Requests\StoreBoardRequest;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Boards/Index', [
            'boards' => $request->user()->ownedBoards()
                ->with('columns')
                ->latest()
                ->get()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Boards/Create', [
            'templates' => BoardTemplate::all()
        ]);
    }

    public function store(StoreBoardRequest $request): RedirectResponse
    {
        $template = BoardTemplate::findOrFail($request->board_template_id);

        return DB::transaction(function () use ($request, $template) {
            $board = Board::create([
                'name' => $request->name,
                'description' => $request->description,
                'owner_id' => $request->user()->id,
                'board_template_id' => $template->id,
            ]);

            // Create columns based on template
            collect($template->columns)->each(function ($columnData, $index) use ($board) {
                Column::create([
                    'name' => $columnData['name'],
                    'board_id' => $board->id,
                    'order' => $index,
                ]);
            });

            return redirect()
                ->route('boards.show', $board)
                ->with('success', 'Board created successfully.');
        });
    }

    public function show(Request $request, Board $board): Response
    {
        if ($request->user()->cannot('view', $board)) {
            abort(403);
        }

        return Inertia::render('Boards/Show', [
            'board' => $board->load('columns.replies.user')
        ]);
    }
}
