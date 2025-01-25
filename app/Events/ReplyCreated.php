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

class ReplyCreated implements ShouldBroadcast
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
            'reply' => [
                'id' => $this->reply->id,
                'content' => $this->reply->content,
                'column_id' => $this->reply->column_id,
                'created_at' => $this->reply->created_at,
                'user' => [
                    'id' => $this->reply->user->id,
                    'name' => $this->reply->user->name,
                ],
                'votes' => [],
            ],
        ];
    }
}
