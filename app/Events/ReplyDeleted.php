<?php

namespace App\Events;

use App\Models\Board;
use App\Models\Reply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ReplyDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public Reply $reply,
        public Board $board
    ) {}

    public function broadcastOn(): Channel
    {
        return new PresenceChannel('board.' . $this->board->id);
    }

    public function broadcastWith(): array
    {
        return [
            'reply' => $this->reply->id,
        ];
    }
}
