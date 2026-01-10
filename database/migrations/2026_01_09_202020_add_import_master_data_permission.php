<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Permission
        $permission = Permission::firstOrCreate(['name' => 'import_master_data']);

        // 2. Assign to Super Admin (if exists)
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permission);
        }

        // 3. Assign to Owner/Apoteker (for this user case, maybe 'admin' or custom role, 
        // strictly following user request "supaya tidak semua orang bisa")
        // Default only Super Admin gets it automatically. Others must be assigned.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'import_master_data')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};
