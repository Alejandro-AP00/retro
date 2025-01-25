<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use App\Models\BoardTemplate;
use App\Models\Column;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_boards_page()
    {
        Board::factory()->count(3)->create([
            'owner_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('boards.index'));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Boards/Index')
            ->has('boards', 3)
        );
    }

    public function test_user_can_view_create_board_page()
    {
        BoardTemplate::factory()->count(2)->create();

        $response = $this->actingAs($this->user)
            ->get(route('boards.create'));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Boards/Create')
            ->has('templates', 2)
        );
    }

    public function test_user_can_create_board_from_template()
    {
        $template = BoardTemplate::factory()->create([
            'columns' => [
                ['name' => 'Column 1'],
                ['name' => 'Column 2'],
                ['name' => 'Column 3'],
            ]
        ]);

        $boardData = [
            'name' => 'My New Board',
            'description' => 'Board description',
            'board_template_id' => $template->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('boards.store'), $boardData);

        $board = Board::first();

        $this->assertDatabaseHas('boards', [
            'name' => $boardData['name'],
            'description' => $boardData['description'],
            'owner_id' => $this->user->id,
            'board_template_id' => $template->id,
        ]);

        $this->assertCount(3, $board->columns);
        $this->assertEquals('Column 1', $board->columns->first()->name);

        $response->assertRedirect(route('boards.show', $board));
    }

    public function test_user_can_view_own_board()
    {
        $board = Board::factory()
            ->has(Column::factory()->count(3))
            ->create([
                'owner_id' => $this->user->id
            ]);

        $response = $this->actingAs($this->user)
            ->get(route('boards.show', $board));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Boards/Show')
            ->has('board.columns')
        );
    }

    public function test_user_cannot_view_others_board()
    {
        $otherUser = User::factory()->create();
        $board = Board::factory()->create([
            'owner_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('boards.show', $board));

        $response->assertForbidden();
    }

    public function test_board_requires_valid_template()
    {
        $response = $this->actingAs($this->user)
            ->post(route('boards.store'), [
                'name' => 'Test Board',
                'board_template_id' => 999
            ]);

        $response->assertSessionHasErrors(['board_template_id']);
    }
}
