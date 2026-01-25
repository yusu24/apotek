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
            if (class_exists(\Spatie\Permission\Models\Permission::class)) {
                $permissionName = 'view financial overview';
                $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);

                $roles = ['Super Admin', 'Admin'];
                foreach ($roles as $roleName) {
                    $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                    if ($role) {
                        // Check if role has the permission using the model's method
                        if (!\DB::table('role_has_permissions')
                            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                            ->where('role_has_permissions.role_id', $role->id)
                            ->where('permissions.name', $permissionName)
                            ->exists()) {
                            $role->givePermissionTo($permissionName);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Hotfix Migration Permission Error: " . $e->getMessage());
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
