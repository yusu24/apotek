<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Dashboard & Reports
            'view dashboard',
            'view reports',
            'export reports',
            
            // Master Data
            'manage master data',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view categories',
            'manage categories',
            'view units',
            'manage units',
            'view suppliers',
            'manage suppliers',
            'manage product units',
            
            // Inventory & Stock
            'manage inventory',
            'view stock',
            'adjust stock',
            'view stock movements',
            'manage expired products',
            
            // Purchasing
            'manage purchasing',
            'create purchase',
            'approve purchase',
            'receive stock',
            
            // POS & Sales
            'access pos',
            'create sale',
            'void transaction',
            'view sales history',
            
            // Returns
            'manage returns',
            'create return',
            'approve return',
            
            // User Management
            'manage users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
            
            // Finance
            'manage finance',
            'view finance',
            'manage expense categories',
            
            // Settings & Configuration
            'manage settings',
            'view settings',
            'edit store profile',
            
            // Audit & Security
            'view audit log',
            'view activity log',
            'manage security',
            
            // System Maintenance
            'backup database',
            'restore database',
            'system maintenance',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleSuperAdmin->syncPermissions(Permission::all());

        // Other roles start with NO permissions by default (as per user request)
        // Access must be granted individually via User Management
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions([]);

        $roleKasir = Role::firstOrCreate(['name' => 'kasir']);
        $roleKasir->syncPermissions([]);

        $roleGudang = Role::firstOrCreate(['name' => 'gudang']);
        $roleGudang->syncPermissions([]);
    }
}
