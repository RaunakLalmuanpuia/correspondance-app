<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view-issue','create-issue', 'edit-issue', 'delete-issue',
            'view-receipt','create-receipt', 'edit-receipt', 'delete-receipt',
            'view-user','create-user','delete-user','edit-user',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $roles = [

            'Admin' => ['view-issue','create-issue', 'edit-issue', 'delete-issue',
                'view-receipt','create-receipt', 'edit-receipt', 'delete-receipt',
                'view-user','create-user','delete-user','edit-user',
            ],

            'Dealing'=>[
                'view-issue','create-issue', 'edit-issue', 'delete-issue',
                'view-receipt','create-receipt', 'edit-receipt', 'delete-receipt',
            ],

            'Viewer'=>[
                'view-issue','create-issue', 'edit-issue', 'delete-issue',
                'view-receipt','create-receipt', 'edit-receipt', 'delete-receipt',
            ]

        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
