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
            'create boards',
            'edit boards',
            'delete boards',
            'lock boards',
            'create templates',
            'edit templates',
            'delete templates',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create global roles
        $adminRole = Role::create(['name' => 'admin', 'team_id' => null]);
        $memberRole = Role::create(['name' => 'member', 'team_id' => null]);

        $adminRole->givePermissionTo($permissions);
    }
}
