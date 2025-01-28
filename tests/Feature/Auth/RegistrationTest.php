<?php

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Providers\RouteServiceProvider;

beforeEach(function(){
    [$this->user, $this->team] = $this->createUserWithTeam('admin');
});

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration page shows invitation details', function () {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->get(route('register', ['invitation' => $invitation->token]));

    $response->assertInertia(fn ($assert) => $assert
        ->component('Auth/Register')
        ->has('invitation', fn ($assert) => $assert
            ->where('email', 'test@example.com')
            ->where('team', 'Test Team')
        )
    );
});

test('user can register with invitation', function () {
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'invitation_token' => $invitation->token,
    ]);

    expect(auth()->check())->toBeTrue();

    $user = User::where('email', 'test@example.com')->first();
    expect($user->teams->pluck('name'))->toContain($team->name)
        ->and($user->hasRole('member', 'web'))->toBeTrue();

    expect($invitation->fresh()->registered_at)->not->toBeNull();

    $response->assertRedirect(route('dashboard', absolute: false));
});

test('cannot register with expired invitation', function () {
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'email' => 'test@example.com',
        'expires_at' => now()->subDay(),
    ]);

    $response = $this->get(route('register', ['invitation' => $invitation->token]));

    $response->assertStatus(403);
});

test('invitation token must be valid', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'invitation_token' => 'invalid-token',
    ]);

    $response->assertSessionHasErrors(['invitation_token']);
});
