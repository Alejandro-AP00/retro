<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Column;
use App\Events\ReplyCreated;
use App\Events\ReplyDeleted;
use App\Http\Requests\StoreReplyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(StoreReplyRequest $request): JsonResponse
    {
        $column = Column::findOrFail($request->column_id);

        if ($request->user()->cannot('create', [Reply::class, $column])) {
            return response()->json(['message' => 'This board is locked or you don\'t have access.'], 403);
        }

        $reply = Reply::create([
            'content' => $request->content,
            'column_id' => $column->id,
            'user_id' => $request->user()->id,
        ]);

        $reply->load(['user', 'votes']);
        broadcast(new ReplyCreated($reply, $column->board))->toOthers();

        return response()->json(['reply' => $reply], 201);
    }

    public function destroy(Request $request, Reply $reply): JsonResponse
    {
        if ($request->user()->cannot('delete', $reply)) {
            return response()->json([], 403);
        }

        $board = $reply->column->board;

        $reply->delete();

        broadcast(new ReplyDeleted($reply, $board))->toOthers();

        return response()->json([], 204);
    }
}
