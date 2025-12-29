<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ActivityLogPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permission
        Permission::firstOrCreate(['name' => 'view activity logs']);

        // Assign to Super Admin
        $role = Role::findByName('super-admin');
        if($role) {
            $role->givePermissionTo('view activity logs');
        }
    }
}
