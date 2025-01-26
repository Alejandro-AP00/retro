<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BoardTemplate;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTemplateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Team $team;

    protected function setUp(): void
    {
        parent::setUp();
        [$this->user, $this->team] = $this->createUserWithTeam('admin');
    }

    public function test_user_can_only_view_team_templates()
    {
        // Create templates for current team
        BoardTemplate::factory()->count(3)->create([
            'team_id' => $this->team->id
        ]);

        // Create templates for another team
        $otherTeam = Team::create(['name' => 'Other Team']);
        BoardTemplate::factory()->count(2)->create([
            'team_id' => $otherTeam->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('templates.index'));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Templates/Index')
            ->has('templates', 3)
        );
    }

    public function test_template_is_created_with_current_team()
    {
        $templateData = [
            'name' => 'Sprint Retrospective',
            'description' => 'Basic sprint retrospective template',
            'columns' => [
                ['name' => 'What went well?'],
                ['name' => 'What could be improved?'],
                ['name' => 'Action items'],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('templates.store'), $templateData);

        $this->assertDatabaseHas('board_templates', [
            'name' => $templateData['name'],
            'team_id' => $this->team->id,
        ]);
    }

    public function test_user_cannot_edit_template_from_different_team()
    {
        $otherTeam = Team::create(['name' => 'Other Team']);
        $template = BoardTemplate::factory()->create([
            'team_id' => $otherTeam->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('templates.edit', $template));

        $response->assertForbidden();
    }

    public function test_user_can_view_create_template_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('templates.create'));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Templates/Create')
        );
    }

    public function test_user_can_create_board_template()
    {
        $templateData = [
            'name' => 'Sprint Retrospective',
            'description' => 'Basic sprint retrospective template',
            'columns' => [
                ['name' => 'What went well?'],
                ['name' => 'What could be improved?'],
                ['name' => 'Action items'],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('templates.store'), $templateData);

        $response->assertRedirect(route('templates.index'));
        $this->assertDatabaseHas('board_templates', [
            'name' => $templateData['name'],
            'description' => $templateData['description'],
        ]);
    }

    public function test_user_can_view_edit_template_page()
    {
        $template = BoardTemplate::factory()
            ->forTeam($this->team)
            ->create();

        $response = $this->actingAs($this->user)
            ->get(route('templates.edit', $template));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Templates/Edit')
            ->has('template')
        );
    }

    public function test_user_can_update_board_template()
    {
        $template = BoardTemplate::factory()
            ->forTeam($this->team)
            ->create();

        $updateData = [
            'name' => 'Updated Template',
            'columns' => [
                ['name' => 'New Column 1'],
                ['name' => 'New Column 2'],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('templates.update', $template), $updateData);

        $response->assertRedirect(route('templates.index'));
        $this->assertDatabaseHas('board_templates', [
            'id' => $template->id,
            'name' => $updateData['name'],
            'team_id' => $this->team->id,
        ]);
    }

    public function test_user_can_delete_board_template()
    {
        $template = BoardTemplate::factory()
            ->forTeam($this->team)
            ->create();

        $response = $this->actingAs($this->user)
            ->delete(route('templates.destroy', $template));

        $response->assertRedirect(route('templates.index'));
        $this->assertDatabaseMissing('board_templates', ['id' => $template->id]);
    }

    public function test_template_requires_valid_data()
    {
        $response = $this->actingAs($this->user)
            ->post(route('templates.store'), [
                'name' => '',
                'columns' => []
            ]);

        $response->assertSessionHasErrors(['name', 'columns']);
    }
}
