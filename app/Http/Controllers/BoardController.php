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
        if ($request->user()->cannot('viewAny', Board::class)) {
            abort(403);
        }

        return Inertia::render('Boards/Index', [
            'boards' => Board::with('columns')
                ->where('team_id', $request->user()->current_team_id)
                ->latest()
                ->get()
        ]);
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

    public function create(Request $request): Response
    {
        return Inertia::render('Boards/Create', [
            'templates' => BoardTemplate::where('team_id', $request->user()->current_team_id)->get()
        ]);
    }

    public function store(StoreBoardRequest $request): RedirectResponse
    {
        $template = BoardTemplate::findOrFail($request->board_template_id);

        if ($request->user()->cannot('create', [Board::class, $template])) {
            abort(403);
        }


        return DB::transaction(function () use ($request, $template) {
            $board = Board::create([
                'name' => $request->name,
                'description' => $request->description,
                'owner_id' => $request->user()->id,
                'board_template_id' => $template->id,
                'team_id' => $request->user()->current_team_id,
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
}
