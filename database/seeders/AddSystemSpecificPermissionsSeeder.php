<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddSystemSpecificPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage backups',
            'view online users',
            'view notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roleSuperAdmin = Role::where('name', 'super-admin')->first();
        if ($roleSuperAdmin) {
            $roleSuperAdmin->syncPermissions(Permission::all());
        }
    }
}
