<?php

namespace App\Http\Controllers;

use App\Events\ReplyRead;
use App\Models\Reply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReplyReadController extends Controller
{
    public function store(Request $request, Reply $reply): JsonResponse
    {
        if ($request->user()->cannot('toggleRead', $reply)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reply->is_read = !$reply->is_read;
        $reply->save();

        broadcast(new ReplyRead($reply, $reply->column->board))->toOthers();

        return response()->json([
            'is_read' => $reply->is_read
        ]);
    }
}
