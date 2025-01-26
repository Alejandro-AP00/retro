<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Events\ReplyVoteUpdated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReplyVoteController extends Controller
{
    public function store(Request $request, Reply $reply): JsonResponse
    {
        if ($request->user()->cannot('vote', $reply)) {
            return response()->json(['message' => 'This board is locked or you don\'t have access.'], 403);
        }

        if ($request->user()->votedReplies()->where('reply_id', $reply->id)->exists()) {
            $request->user()->votedReplies()->detach($reply);
        } else {
            $request->user()->votedReplies()->attach($reply);
        }

        $reply->load('votes.user');
        broadcast(new ReplyVoteUpdated($reply, $reply->column->board))->toOthers();

        return response()->json([
            'votes' => $reply->votes
        ]);
    }
}
