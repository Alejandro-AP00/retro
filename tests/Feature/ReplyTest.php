<?php

namespace Tests\Feature;

use App\Events\ReplyCreated;
use App\Events\ReplyDeleted;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Models\Reply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Board $board;
    private Column $column;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->board = Board::factory()->create(['locked_at' => null]);
        $this->column = Column::factory()->create(['board_id' => $this->board->id]);
    }

    public function test_user_can_create_reply()
    {
        Event::fake([ReplyCreated::class]);

        $response = $this->actingAs($this->user)
            ->postJson(route('replies.store'), [
                'content' => 'Test reply content',
                'column_id' => $this->column->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'reply' => [
                    'id',
                    'content',
                    'column_id',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'name'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('replies', [
            'content' => 'Test reply content',
            'user_id' => $this->user->id,
            'column_id' => $this->column->id,
        ]);

        Event::assertDispatched(ReplyCreated::class, function ($event) {
            return $event->reply->content === 'Test reply content';
        });
    }

    public function test_user_cannot_create_reply_on_locked_board()
    {
        Event::fake([ReplyCreated::class]);
        $this->board->update(['locked_at' => now()->subDay()]);

        $response = $this->actingAs($this->user)
            ->postJson(route('replies.store'), [
                'content' => 'Test reply content',
                'column_id' => $this->column->id,
            ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'This board is locked.']);

        $this->assertDatabaseMissing('replies', [
            'content' => 'Test reply content',
        ]);

        Event::assertNotDispatched(ReplyCreated::class);
    }

    public function test_user_can_delete_own_reply()
    {
        Event::fake([ReplyDeleted::class]);

        $reply = Reply::factory()->create([
            'user_id' => $this->user->id,
            'column_id' => $this->column->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('replies.destroy', $reply));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        Event::assertDispatched(ReplyDeleted::class, function ($event) use ($reply) {
            return $event->reply->id === $reply->id &&
                   $event->board->id === $this->board->id;
        });
    }

    public function test_board_owner_can_delete_any_reply()
    {
        Event::fake([ReplyDeleted::class]);

        $otherUser = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $this->user->id]);
        $column = Column::factory()->create(['board_id' => $board->id]);
        $reply = Reply::factory()->create([
            'user_id' => $otherUser->id,
            'column_id' => $column->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('replies.destroy', $reply));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        Event::assertDispatched(ReplyDeleted::class, function ($event) use ($reply, $board) {
            return $event->reply->id === $reply->id &&
                   $event->board->id === $board->id;
        });
    }

    public function test_user_cannot_delete_others_reply()
    {
        Event::fake([ReplyDeleted::class]);

        $otherUser = User::factory()->create();
        $reply = Reply::factory()->create([
            'user_id' => $otherUser->id,
            'column_id' => $this->column->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('replies.destroy', $reply));

        $response->assertStatus(403);

        $this->assertDatabaseHas('replies', ['id' => $reply->id]);

        Event::assertNotDispatched(ReplyDeleted::class);
    }
}
