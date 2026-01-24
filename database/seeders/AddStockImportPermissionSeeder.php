<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddStockImportPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the new permission
        $permissionName = 'import stock';

        // Create permission if it doesn't exist
        $permission = Permission::firstOrCreate(['name' => $permissionName]);

        // Assign to Super Admin and Admin roles
        $roles = ['Super Admin', 'Admin'];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if (!$role->hasPermissionTo($permissionName)) {
                $role->givePermissionTo($permissionName);
                $this->command->info("Granted '$permissionName' to '$roleName'");
            }
        }
    }
}
