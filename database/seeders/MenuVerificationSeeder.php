<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Expense;
use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MenuVerificationSeeder extends Seeder
{
    public function run(): void
    {
        $accountingService = new AccountingService();

        // 1. Administration: Users & Roles
        $this->command->info('Creating administration data...');
        
        $admin = User::firstOrCreate(
            ['email' => 'admin_test@apotek.com'],
            [
                'name' => 'Admin Tester',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        $kasir = User::firstOrCreate(
            ['email' => 'kasir_test@apotek.com'],
            [
                'name' => 'Kasir Tester',
                'password' => Hash::make('password'),
            ]
        );
        $kasir->assignRole('kasir');

        // 2. Administration: Settings
        Setting::set('store_name', 'APOTEK VERIFIKASI SYSTEM');
        Setting::set('store_address', 'Lab Testing Center, Building 404');
        
        // 3. Administration: Activity Logs
        ActivityLog::log([
            'user_id' => $admin->id,
            'action' => 'login',
            'module' => 'Auth',
            'description' => 'Admin Tester logged in for verification',
        ]);

        // 4. Finance: Setup Master Data for Finance
        $this->command->info('Creating master data for finance...');
        $category = Category::firstOrCreate(['name' => 'Obat Keras']);
        $unit = Unit::firstOrCreate(['name' => 'Tablet']);
        $supplier = Supplier::firstOrCreate(['name' => 'PBF Test Seeder'], ['phone' => '021-999-888']);

        $product = Product::firstOrCreate(['barcode' => 'TEST-FIN-001'], [
            'name' => 'Medicine Test Financial',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'slug' => Str::slug('Medicine Test Financial ' . time()),
            'sell_price' => 25000,
        ]);

        // 5. Finance: Sales (Historical) - Last 3 Months
        $this->command->info('Creating historical sales...');
        for ($i = 0; $i < 15; $i++) {
            $date = Carbon::now()->subDays(rand(1, 90));
            $sale = Sale::create([
                'user_id' => $kasir->id,
                'invoice_no' => 'INV-VERIF-' . $date->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'date' => $date,
                'total_amount' => 100000,
                'grand_total' => 100000,
                'payment_method' => ['cash', 'qris', 'transfer'][rand(0, 2)],
                'status' => 'completed',
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => 4,
                'sell_price' => 25000,
                'subtotal' => 100000,
            ]);

            // Post to Journal
            $accountingService->postSaleJournal($sale->id);
        }

        // 6. Finance: Purchases (Goods Receipts)
        $this->command->info('Creating goods receipts...');
        // Paid Receipt
        $grPaid = GoodsReceipt::create([
            'delivery_note_number' => 'SJ-VERIF-PAID-001',
            'received_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'user_id' => $admin->id,
            'payment_method' => 'cash',
            'total_amount' => 500000,
            'paid_amount' => 500000,
            'payment_status' => 'paid',
        ]);
        GoodsReceiptItem::create([
            'goods_receipt_id' => $grPaid->id,
            'product_id' => $product->id,
            'qty_received' => 50,
            'buy_price' => 10000,
            'batch_no' => 'BATCH-V1',
            'expired_date' => Carbon::now()->addYear(),
        ]);
        $accountingService->postPurchaseJournal($grPaid->id);

        // Pending Receipt (Debt)
        $grDebt = GoodsReceipt::create([
            'delivery_note_number' => 'SJ-VERIF-DEBT-002',
            'received_date' => Carbon::now()->subDays(45)->format('Y-m-d'),
            'user_id' => $admin->id,
            'payment_method' => 'due_date',
            'total_amount' => 1000000,
            'paid_amount' => 0,
            'payment_status' => 'pending',
            'due_date' => Carbon::now()->addDays(15),
        ]);
        GoodsReceiptItem::create([
            'goods_receipt_id' => $grDebt->id,
            'product_id' => $product->id,
            'qty_received' => 100,
            'buy_price' => 10000,
            'batch_no' => 'BATCH-V2',
            'expired_date' => Carbon::now()->addYear(),
        ]);
        $accountingService->postPurchaseJournal($grDebt->id);

        // 7. Finance: Expenses
        $this->command->info('Creating expenses...');
        $cashAccount = Account::where('code', '1-1100')->first();
        if ($cashAccount) {
            $expense = Expense::create([
                'description' => 'Biaya Verifikasi System',
                'date' => Carbon::now()->subDays(5),
                'amount' => 50000,
                'category' => 'Operasional',
                'account_id' => $cashAccount->id,
                'user_id' => $admin->id,
            ]);
            $accountingService->postExpenseJournal($expense->id, $cashAccount->id);
        }

        $this->command->info('✓ Menu Verification Seeder completed successfully');
    }
}
