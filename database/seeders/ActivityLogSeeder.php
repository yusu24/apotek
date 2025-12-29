<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        $modules = ['products', 'sales', 'inventory', 'users', 'expenses', 'procurement', 'settings'];
        $actions = ['created', 'updated', 'deleted', 'viewed', 'exported', 'login', 'logout'];
        
        $descriptions = [
            'products' => [
                'created' => 'Created new product: Paracetamol 500mg',
                'updated' => 'Updated product price for Amoxicillin',
                'deleted' => 'Deleted product: Expired Medicine',
                'viewed' => 'Viewed product details',
                'exported' => 'Exported product list to Excel',
            ],
            'sales' => [
                'created' => 'Created new sale transaction #12345',
                'updated' => 'Updated sale #12346',
                'deleted' => 'Deleted sale transaction #12347',
                'viewed' => 'Viewed sales report',
                'exported' => 'Exported sales data',
            ],
            'inventory' => [
                'created' => 'Added new stock batch',
                'updated' => 'Adjusted stock quantity',
                'deleted' => 'Removed expired batch',
                'viewed' => 'Viewed stock history',
                'exported' => 'Exported inventory report',
            ],
            'users' => [
                'created' => 'Created new user account',
                'updated' => 'Updated user permissions',
                'deleted' => 'Deleted user account',
                'viewed' => 'Viewed user list',
                'login' => 'User logged in',
                'logout' => 'User logged out',
            ],
            'expenses' => [
                'created' => 'Added new expense: Electricity Bill',
                'updated' => 'Updated expense amount',
                'deleted' => 'Deleted expense record',
                'viewed' => 'Viewed expense report',
                'exported' => 'Exported expense data',
            ],
            'procurement' => [
                'created' => 'Created purchase order #PO-001',
                'updated' => 'Updated PO status',
                'deleted' => 'Cancelled purchase order',
                'viewed' => 'Viewed goods receipt',
                'exported' => 'Exported procurement report',
            ],
            'settings' => [
                'updated' => 'Updated store settings',
                'viewed' => 'Viewed system settings',
            ],
        ];

        // Create sample logs for the past 30 days
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $module = $modules[array_rand($modules)];
            $action = $actions[array_rand($actions)];
            
            // Skip if description doesn't exist for this combination
            if (!isset($descriptions[$module][$action])) {
                continue;
            }

            $oldValues = null;
            $newValues = null;

            // Add sample old/new values for update actions
            if ($action === 'updated') {
                $oldValues = [
                    'name' => 'Old Product Name',
                    'price' => 10000,
                    'stock' => 50,
                ];
                $newValues = [
                    'name' => 'New Product Name',
                    'price' => 12000,
                    'stock' => 45,
                ];
            } elseif ($action === 'created') {
                $newValues = [
                    'name' => 'New Item',
                    'price' => 15000,
                    'stock' => 100,
                ];
            } elseif ($action === 'deleted') {
                $oldValues = [
                    'name' => 'Deleted Item',
                    'price' => 8000,
                    'stock' => 0,
                ];
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'module' => $module,
                'description' => $descriptions[$module][$action],
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'url' => 'http://localhost:8000/' . $module,
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ]);
        }

        $this->command->info('Activity logs seeded successfully!');
    }
}
