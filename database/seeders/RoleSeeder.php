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
            'view sales reports',
            'view profit loss',
            'view balance sheet',
            'view income statement',
            'view ppn report',
            'view product margin report',
            'view ap aging report',
            'view journals',
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
            'upload product images',
            
            // Inventory & Stock
            'manage inventory',
            'view stock',
            'adjust stock',
            'view stock movements',
            'manage expired products',
            
            // Purchasing / Procurement
            'manage purchasing',
            'view purchase orders',
            'view goods receipts',
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
            'view expenses',
            'manage expense categories',
            'manage opening balances',

            // Accounting
            'view accounts',
            'manage accounts',
            'create journal',
            'view general ledger',
            
            // Settings & Configuration
            'manage settings',
            'view settings',
            'edit store profile',
            'manage pos settings',
            'view guide',
            
            // Audit & Security
            'view audit log',
            'view activity logs',
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
        $roleKasir->syncPermissions([
            'access pos',
            'create sale',
            'view stock',
            'view sales history',
            'view dashboard',
        ]);

        $roleGudang = Role::firstOrCreate(['name' => 'gudang']);
        $roleGudang->syncPermissions([]);
    }
}
