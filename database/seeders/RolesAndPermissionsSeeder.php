<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view.boards',
            'create.boards',
            'edit.boards',
            'delete.boards',
            'lock.boards',

            'view.templates',
            'create.templates',
            'edit.templates',
            'delete.templates',

            'edit.teams',
            'invite.members',
            'update.members',
            'remove.members',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create global roles
        $adminRole = Role::create(['name' => 'admin', 'team_id' => null]);
        $memberRole = Role::create(['name' => 'member', 'team_id' => null]);

        $adminRole->givePermissionTo($permissions);
        $memberRole->givePermissionTo([
            'view.boards',
        ]);
    }
}
