<?php

namespace App\Events;

use App\Models\Reply;
use App\Models\Board;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ReplyMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public Reply $reply,
        public Board $board,
        public int $oldColumnId,
        public int $newColumnId,
        public int $newOrder
    ) {}

    public function broadcastOn(): Channel
    {
        return new PresenceChannel('board.' . $this->board->id);
    }

    public function broadcastWith(): array
    {
        return [
            'reply' => $this->reply->load('user', 'votes'),
            'oldColumnId' => $this->oldColumnId,
            'newColumnId' => $this->newColumnId,
            'newOrder' => $this->newOrder,
        ];
    }
}
