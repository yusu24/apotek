<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AddDashboardFinancialPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view dashboard receivables',
            'view dashboard payables',
        ];

        $guardName = 'web';

        foreach ($permissions as $permissionName) {
            // Ensure permission exists
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guardName,
            ]);

            $this->command->info("Permission '$permissionName' ensured.");
        }

        // Assign to Super Admin (using syncPermissions to append)
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
            $this->command->info("Permissions assigned to Super Admin.");
        }

        // Clear cache
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Ignore cache errors
        }
    }
}
