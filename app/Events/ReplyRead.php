<?php

namespace App\Events;

use App\Models\Board;
use App\Models\Reply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReplyRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
            'replyId' => $this->reply->id,
            'isRead' => $this->reply->is_read,
        ];
    }
}
