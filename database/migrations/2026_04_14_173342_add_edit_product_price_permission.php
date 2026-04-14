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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionName = 'edit product price';
        
        // Create permission if it doesn't exist
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName]);

        // Assign to super-admin if role exists
        $role = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
        if ($role) {
            $role->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionName = 'edit product price';
        
        $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
        if ($permission) {
            $permission->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
