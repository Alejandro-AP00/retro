<?php

namespace Tests\Feature;

use App\Events\ReplyVoteUpdated;
use App\Models\User;
use App\Models\Reply;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReplyVoteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Reply $reply;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $board = Board::factory()->create(['locked_at' => null]);
        $column = Column::factory()->create(['board_id' => $board->id]);
        $this->reply = Reply::factory()->create(['column_id' => $column->id]);
    }

    public function test_user_can_vote_on_reply()
    {
        Event::fake([ReplyVoteUpdated::class]);

        $response = $this->actingAs($this->user)
            ->postJson(route('replies.votes.store', $this->reply));

        $response->assertStatus(200)
            ->assertJsonStructure(['votes']);

        $this->assertDatabaseHas('reply_votes', [
            'user_id' => $this->user->id,
            'reply_id' => $this->reply->id,
        ]);

        Event::assertDispatched(ReplyVoteUpdated::class, function ($event) {
            return $event->reply->id === $this->reply->id;
        });
    }

    public function test_user_can_remove_vote_from_reply()
    {
        Event::fake([ReplyVoteUpdated::class]);

        // First add the vote
        $this->user->votedReplies()->attach($this->reply);

        $response = $this->actingAs($this->user)
            ->postJson(route('replies.votes.store', $this->reply));

        $response->assertStatus(200)
            ->assertJsonStructure(['votes']);

        $this->assertDatabaseMissing('reply_votes', [
            'user_id' => $this->user->id,
            'reply_id' => $this->reply->id,
        ]);

        Event::assertDispatched(ReplyVoteUpdated::class, function ($event) {
            return $event->reply->id === $this->reply->id;
        });
    }

    public function test_user_cannot_vote_on_locked_board()
    {
        Event::fake([ReplyVoteUpdated::class]);

        $board = $this->reply->column->board;
        $board->update(['locked_at' => now()->subDay()]);

        $response = $this->actingAs($this->user)
            ->postJson(route('replies.votes.store', $this->reply));

        $response->assertStatus(403)
            ->assertJson(['message' => 'This board is locked.']);

        $this->assertDatabaseMissing('reply_votes', [
            'user_id' => $this->user->id,
            'reply_id' => $this->reply->id,
        ]);

        Event::assertNotDispatched(ReplyVoteUpdated::class);
    }
}
