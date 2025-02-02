<?php

namespace Tests;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    protected function createUserWithTeam(string $role = 'member'): array
    {
        // Create roles if they don't exist
        if (!Role::where('name', 'admin')->exists()) {
            $adminRole = Role::create(['name' => 'admin']);
            $memberRole = Role::create(['name' => 'member']);

            // Create permissions
            $permissions = [
                'create.boards',
                'edit.boards',
                'delete.boards',
                'lock.boards',
                'create.templates',
                'edit.templates',
                'delete.templates',
            ];

            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
            }

            $adminRole->givePermissionTo(Permission::all());
            $memberRole->givePermissionTo(['create.boards']);
        }

        $user = User::factory()->create();
        $team = Team::create(['name' => 'Test Team']);
        $user->teams()->attach($team);
        $user->switchTeam($team);
        $user->assignRole($role);

        return [$user, $team];
    }
}
