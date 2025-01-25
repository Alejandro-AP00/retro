<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BoardTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTemplateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_board_templates_page()
    {
        BoardTemplate::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('templates.index'));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Templates/Index')
            ->has('templates', 3)
        );
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
        $template = BoardTemplate::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('templates.edit', $template));

        $response->assertInertia(fn ($assert) => $assert
            ->component('Templates/Edit')
            ->has('template')
        );
    }

    public function test_user_can_update_board_template()
    {
        $template = BoardTemplate::factory()->create();
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
        ]);
    }

    public function test_user_can_delete_board_template()
    {
        $template = BoardTemplate::factory()->create();

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
