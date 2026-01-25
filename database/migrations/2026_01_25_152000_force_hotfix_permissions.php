<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Schema Fix: Ensure is_developer exists
        if (!Schema::hasColumn('users', 'is_developer')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_developer')->default(false)->after('password');
            });
        }

        // 2. Data Fix: Ensure 'view financial overview' permission exists
        try {
            $permissionName = 'view financial overview';
            $guardName = 'web';

            // Check if permission exists using raw DB
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

            // Assign to roles
            $roles = \DB::table('roles')->whereIn('name', ['Super Admin', 'Admin'])->get();
            foreach ($roles as $role) {
                \DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $role->id,
                ]);
            }

            // 3. Clear Spatie Permission Cache
            if (app()->bound(\Spatie\Permission\PermissionRegistrar::class)) {
                app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            }
        } catch (\Exception $e) {
            \Log::error("Hotfix Migration Permission Error (Raw DB): " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_developer')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_developer');
            });
        }
    }
};
