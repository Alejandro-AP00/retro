<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class TeamManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;
    private Team $team;

    protected function setUp(): void
    {
        parent::setUp();
        [$this->admin, $this->team] = $this->createUserWithTeam('admin');
        $this->member = User::factory()->create();
        $this->member->teams()->attach($this->team);
        $this->member->switchTeam($this->team);
        $this->member->assignRole('member');
    }

    public function test_admin_can_update_team_name()
    {
        $response = $this->actingAs($this->admin)
            ->put(route('teams.update', $this->team), [
                'name' => 'Updated Team Name'
            ]);

        $response->assertRedirect();
        $this->assertEquals('Updated Team Name', $this->team->fresh()->name);
    }

    public function test_member_cannot_update_team_name()
    {
        $response = $this->actingAs($this->member)
            ->put(route('teams.update', $this->team), [
                'name' => 'Updated Team Name'
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_invite_team_member()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)
            ->post(route('team-invitations.store', $this->team), [
                'email' => 'test@example.com',
                'role' => 'member'
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('team_invitations', [
            'team_id' => $this->team->id,
            'email' => 'test@example.com',
            'role_id' => Role::where('name', 'member')->first()->id,
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            TeamInvitationNotification::class
        );
    }

    public function test_member_cannot_invite_team_member()
    {
        $response = $this->actingAs($this->member)
            ->post(route('team-invitations.store', $this->team), [
                'email' => 'test@example.com',
                'role' => 'member'
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_update_member_role()
    {
        $newRole = Role::create(['name' => 'moderator']);

        $response = $this->actingAs($this->admin)
            ->put(route('team-members.update', [$this->team, $this->member]), [
                'role' => $newRole->name
            ]);

        $response->assertRedirect();
        $this->assertTrue($this->member->hasRole($newRole->name, 'web'));
    }

    public function test_member_cannot_update_member_role()
    {
        $otherMember = User::factory()->create();
        $this->team->users()->attach($otherMember);
        $otherMember->switchTeam($this->team);
        $otherMember->syncRoles(['member']);

        $response = $this->actingAs($this->member)
            ->put(route('team-members.update', [$this->team, $otherMember]), [
                'role' => 'admin'
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_remove_team_member()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('team-members.destroy', [$this->team, $this->member]));

        $response->assertRedirect();
        $this->assertFalse($this->team->fresh()->users->contains($this->member));
    }

    public function test_member_can_leave_team()
    {
        $response = $this->actingAs($this->member)
            ->delete(route('team-members.destroy', [$this->team, $this->member]));

        $response->assertRedirect();
        $this->assertFalse($this->team->fresh()->users->contains($this->member));
    }

    public function test_invitation_can_be_accepted()
    {
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $this->team->id,
            'email' => 'test@example.com',
            'expires_at' => now()->addDay(),
        ]);

        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->actingAs($user)
            ->get(route('team-invitations.accept', $invitation));

        $response->assertRedirect();
        $this->assertTrue($this->team->fresh()->users->contains($user));
        $this->assertTrue($user->hasRole('member', 'web'));
        $this->assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
    }

    public function test_expired_invitation_cannot_be_accepted()
    {
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $this->team->id,
            'email' => 'test@example.com',
            'expires_at' => now()->subDay(),
        ]);

        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->actingAs($user)
            ->get(route('team-invitations.accept', $invitation));

        $response->assertRedirect();
        $this->assertFalse($this->team->fresh()->users->contains($user));
    }

    public function test_invitation_validates_role()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('team-invitations.store', $this->team), [
                'email' => 'test@example.com',
                'role_id' => 'non-existent-role'
            ]);

        $response->assertSessionHasErrors(['role']);
    }

    public function test_invitation_accepts_any_valid_role()
    {
        $newRole = Role::create(['name' => 'moderator']);

        $response = $this->actingAs($this->admin)
            ->post(route('team-invitations.store', $this->team), [
                'email' => 'test@example.com',
                'role' => $newRole->name
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('team_invitations', [
            'team_id' => $this->team->id,
            'email' => 'test@example.com',
            'role_id' => $newRole->id
        ]);
    }
}
