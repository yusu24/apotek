<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddMissingPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage accounts',
            'view reports',
            'import accounts',
            'import products',
            'import suppliers',
        ];

        $guardName = 'web';

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guardName,
            ]);
        }

        // super-admin gets every permission (mirrors RoleSeeder's syncPermissions(Permission::all()))
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Clean up a phantom duplicate 'Admin' role accidentally created by an earlier
        // seeder using the wrong casing (real role is 'admin', lowercase). Only removed
        // if nobody was ever assigned to it.
        $phantomAdmin = Role::where('name', 'Admin')->first();
        if ($phantomAdmin && $phantomAdmin->users()->count() === 0) {
            $phantomAdmin->delete();
        }

        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Ignore cache errors
        }
    }
}
