<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use App\Models\BoardTemplate;
use App\Models\Column;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Team $team;
    private BoardTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();
        [$this->user, $this->team] = $this->createUserWithTeam('member');

        // Create template for team
        $this->template = BoardTemplate::factory()
            ->forTeam($this->team)
            ->create();
    }

    public function test_user_can_view_only_team_boards()
    {
        // Create boards for current team
        Board::factory()
            ->forTeam($this->team)
            ->count(3)
            ->create([
                'owner_id' => $this->user->id,
            ]);

        // Create boards for another team
        $otherTeam = Team::create(['name' => 'Other Team']);
        Board::factory()
            ->forTeam($otherTeam)
            ->count(2)
            ->create([
                'owner_id' => $this->user->id,
            ]);

        $response = $this->actingAs($this->user)
            ->get(route('boards.index'));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Boards/Index')
            ->has('boards', 3)
        );
    }

    public function test_user_can_only_see_team_templates_on_create_page()
    {
        // Create template for another team
        $otherTeam = Team::create(['name' => 'Other Team']);
        BoardTemplate::factory()->create([
            'team_id' => $otherTeam->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('boards.create'));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Boards/Create')
            ->has('templates', 1)
        );
    }

    public function test_board_is_created_with_current_team()
    {
        $boardData = [
            'name' => 'My New Board',
            'description' => 'Board description',
            'board_template_id' => $this->template->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('boards.store'), $boardData);

        $board = Board::first();

        $this->assertDatabaseHas('boards', [
            'name' => $boardData['name'],
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
        ]);

        $response->assertRedirect(route('boards.show', $board));
    }

    public function test_user_cannot_create_board_with_template_from_different_team()
    {
        $otherTeam = Team::create(['name' => 'Other Team']);
        $otherTemplate = BoardTemplate::factory()->create([
            'team_id' => $otherTeam->id
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('boards.store'), [
                'name' => 'Test Board',
                'board_template_id' => $otherTemplate->id,
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_view_any_board_in_team()
    {
        $this->user->syncRoles(['admin']);

        $otherUser = User::factory()->create();
        $board = Board::factory()->create([
            'owner_id' => $otherUser->id,
            'team_id' => $this->team->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('boards.show', $board));

        $response->assertOk();
    }

    public function test_user_can_view_boards_page()
    {
        Board::factory()
            ->forTeam($this->team)
            ->count(3)
            ->create([
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
        BoardTemplate::factory()
            ->forTeam($this->team)
            ->count(2)
            ->create();

        $response = $this->actingAs($this->user)
            ->get(route('boards.create'));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Boards/Create')
            ->has('templates', 3) // This includes the one created in the set up function
        );
    }

    public function test_user_can_create_board_from_template()
    {
        $template = BoardTemplate::factory()
            ->forTeam($this->team)
            ->create([
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
            'team_id' => $this->team->id,
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
            ->forTeam($this->team)
            ->create([
                'owner_id' => $this->user->id
            ]);

        $response = $this->actingAs($this->user)
            ->get(route('boards.show', $board));
        $response->assertStatus(200);

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
