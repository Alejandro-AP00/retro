<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Column;
use App\Events\ReplyMoved;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReplyPositionController extends Controller
{
    public function update(Reply $reply, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'columnId' => 'required|exists:columns,id',
            'order' => 'required|integer|min:0',
        ]);

        $newColumn = Column::findOrFail($validated['columnId']);

        if ($request->user()->cannot('update', $reply)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $oldColumnId = $reply->column_id;
        $reply->update([
            'column_id' => $validated['columnId'],
            'order_by' => $validated['order']
        ]);

        broadcast(new ReplyMoved(
            $reply,
            $newColumn->board,
            $oldColumnId,
            $validated['columnId'],
            $validated['order']
        ))->toOthers();

        return response()->json(['message' => 'Position updated']);
    }
}
