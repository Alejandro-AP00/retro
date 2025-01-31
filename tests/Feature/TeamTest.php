<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_team()
    {
        $user = User::factory()->create();
        $members = User::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->post(route('teams.store'), [
                'name' => 'New Team',
                'members' => $members->pluck('id')->toArray()
            ]);

        $response->assertRedirect();

        $team = Team::latest()->first();
        $this->assertEquals('New Team', $team->name);
        $this->assertTrue($team->users->contains($user));
        $this->assertTrue($user->hasRole('admin'));

        foreach ($members as $member) {
            $this->assertTrue($team->users->contains($member));
            $this->assertTrue($member->hasRole('member'));
        }
    }

    public function test_team_requires_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('teams.store'), [
                'name' => '',
                'members' => []
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_team_members_must_exist()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('teams.store'), [
                'name' => 'New Team',
                'members' => [999] // Non-existent user ID
            ]);

        $response->assertSessionHasErrors(['members.0']);
    }
}
