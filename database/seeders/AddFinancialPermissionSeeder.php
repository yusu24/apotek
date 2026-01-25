<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddFinancialPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionName = 'view financial overview';
        $guardName = 'web';

        // 1. Ensure permission exists (Raw DB for maximum reliability)
        $permission = \DB::table('permissions')
            ->where('name', $permissionName)
            ->where('guard_name', $guardName)
            ->first();

        if (!$permission) {
            $permissionId = \DB::table('permissions')->insertGetId([
                'name' => $permissionName,
                'guard_name' => $guardName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $permissionId = $permission->id;
        }

        // 2. Assign to roles
        $roles = \DB::table('roles')->whereIn('name', ['Super Admin', 'Admin'])->get();
        foreach ($roles as $role) {
            \DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        // 3. Clear Permission Cache manually
        try {
            \Illuminate\Support\Facades\Cache::forget('spatie.permission.cache');
        } catch (\Exception $e) {
            // Ignore cache errors
        }

        $this->command->info("Successfully ensured '$permissionName' exists and is assigned to Admin roles.");
    }
}
