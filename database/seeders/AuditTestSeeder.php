<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Batch;
use App\Models\FixedAsset;
use App\Models\Setting;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditTestSeeder extends Seeder
{
    public function run()
    {
        $service = new AccountingService();
        $this->command->info('Starting AI Audit & Financial System Testing...');

        // 1. CLEAR EXISTING DATA FOR CLEAN AUDIT
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('journal_entry_lines')->truncate();
        DB::table('journal_entries')->truncate();
        DB::table('stock_movements')->truncate();
        DB::table('batches')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('units')->truncate();
        DB::table('fixed_assets')->truncate();
        DB::table('asset_depreciations')->truncate();
        DB::table('accounts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. CREATE CHART OF ACCOUNTS
        $accounts = [
            // Assets
            ['code' => '1-1100', 'name' => 'Kas', 'type' => 'asset', 'category' => 'current_asset', 'is_system' => true],
            ['code' => '1-1201', 'name' => 'Bank Mandiri', 'type' => 'asset', 'category' => 'current_asset', 'is_system' => false],
            ['code' => '1-1202', 'name' => 'Bank BRI', 'type' => 'asset', 'category' => 'current_asset', 'is_system' => false],
            ['code' => '1-1203', 'name' => 'Bank BNI', 'type' => 'asset', 'category' => 'current_asset', 'is_system' => false],
            ['code' => '1-1204', 'name' => 'Bank BCA', 'type' => 'asset', 'category' => 'current_asset', 'is_system' => false],
            ['code' => '1-1300', 'name' => 'Piutang Usaha', 'type' => 'asset', 'category' => 'current_asset', 'is_system' => true],
            ['code' => '1-1400', 'name' => 'Persediaan Obat', 'type' => 'asset', 'category' => 'current_asset', 'is_system' => true],
            
            // Fixed Assets
            ['code' => '1-3100', 'name' => 'Tanah', 'type' => 'asset', 'category' => 'fixed_asset', 'is_system' => false],
            ['code' => '1-3200', 'name' => 'Bangunan', 'type' => 'asset', 'category' => 'fixed_asset', 'is_system' => false],
            ['code' => '1-3210', 'name' => 'Akumulasi Penyusutan Bangunan', 'type' => 'asset', 'category' => 'fixed_asset', 'is_system' => false],
            ['code' => '1-3300', 'name' => 'Kendaraan', 'type' => 'asset', 'category' => 'fixed_asset', 'is_system' => false],
            ['code' => '1-3310', 'name' => 'Akumulasi Penyusutan Kendaraan', 'type' => 'asset', 'category' => 'fixed_asset', 'is_system' => false],
            ['code' => '1-3400', 'name' => 'Peralatan', 'type' => 'asset', 'category' => 'fixed_asset', 'is_system' => false],
            ['code' => '1-3410', 'name' => 'Akumulasi Penyusutan Peralatan', 'type' => 'asset', 'category' => 'fixed_asset', 'is_system' => false],

            // Liabilities
            ['code' => '2-1100', 'name' => 'Utang Usaha', 'type' => 'liability', 'category' => 'current_liability', 'is_system' => true],
            ['code' => '2-1200', 'name' => 'Utang Jatuh Tempo', 'type' => 'liability', 'category' => 'current_liability', 'is_system' => true],
            
            // Equity
            ['code' => '3-1000', 'name' => 'Modal Pemilik', 'type' => 'equity', 'category' => 'capital', 'is_system' => true],
            ['code' => '3-2000', 'name' => 'Laba Ditahan', 'type' => 'equity', 'category' => 'capital', 'is_system' => true],
            
            // Revenue
            ['code' => '4-1000', 'name' => 'Penjualan Obat', 'type' => 'revenue', 'category' => 'sales', 'is_system' => true],
            ['code' => '4-1100', 'name' => 'Retur Penjualan', 'type' => 'revenue', 'category' => 'sales', 'is_system' => true],
            
            // Expenses
            ['code' => '5-1000', 'name' => 'Harga Pokok Penjualan (COGS)', 'type' => 'expense', 'category' => 'cogs', 'is_system' => true],
            ['code' => '5-2100', 'name' => 'Beban Listrik & Air', 'type' => 'expense', 'category' => 'operating_expense', 'is_system' => false],
            ['code' => '5-2400', 'name' => 'Beban Penyusutan', 'type' => 'expense', 'category' => 'operating_expense', 'is_system' => false],
        ];

        foreach ($accounts as $acc) {
            Account::create($acc);
        }

        // 3. CREATE MASTER DATA (Category, Unit, Products)
        $category = Category::create([
            'name' => 'Obat Umum',
            'slug' => 'obat-umum-audit'
        ]);

        $unit = Unit::create([
            'name' => 'Box'
        ]);

        // Create products for inventory
        $product1 = Product::create([
            'name' => 'Paracetamol 500mg',
            'barcode' => 'AUDIT-PCT-001',
            'slug' => 'paracetamol-500mg-audit',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'sell_price' => 25000,
            'min_stock' => 10,
        ]);

        $product2 = Product::create([
            'name' => 'Amoxicillin 500mg',
            'barcode' => 'AUDIT-AMX-001',
            'slug' => 'amoxicillin-500mg-audit',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'sell_price' => 35000,
            'min_stock' => 10,
        ]);

        // 4. SET OPENING BALANCES
        $mandiri = Account::where('code', '1-1201')->first();
        $bri = Account::where('code', '1-1202')->first();
        $bni = Account::where('code', '1-1203')->first();
        $bca = Account::where('code', '1-1204')->first();
        $inventoryAcc = Account::where('code', '1-1400')->first();
        $capitalAcc = Account::where('code', '3-1000')->first();

        // S0: Initial Balances - Create opening inventory batch
        // Opening inventory: 1000 units @ Rp 16,507.70 per unit = Rp 16,507,700
        $openingBatch = Batch::create([
            'product_id' => $product1->id,
            'batch_no' => 'OPENING-BATCH-001',
            'stock_in' => 1000,
            'stock_current' => 1000,
            'buy_price' => 16507.70,
            'expired_date' => Carbon::now()->addYears(2),
        ]);

        $service->createJournalEntry([
            'date' => Carbon::now()->startOfMonth(),
            'description' => 'Audit Opening Balances',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $mandiri->id, 'debit' => 125000000, 'credit' => 0],
                ['account_id' => $bri->id, 'debit' => 125000000, 'credit' => 0],
                ['account_id' => $bni->id, 'debit' => 125000000, 'credit' => 0],
                ['account_id' => $bca->id, 'debit' => 125000000, 'credit' => 0],
                ['account_id' => $inventoryAcc->id, 'debit' => 16507700, 'credit' => 0],
                ['account_id' => $capitalAcc->id, 'debit' => 0, 'credit' => 516507700],
            ]
        ]);

        // 5. SCENARIO 2: Purchase of goods (Rp 10M: 5M Mandiri, 5M AP)
        // Create batch for purchase: 500 units @ Rp 20,000 = Rp 10,000,000
        $purchaseBatch = Batch::create([
            'product_id' => $product2->id,
            'batch_no' => 'PURCHASE-BATCH-001',
            'stock_in' => 500,
            'stock_current' => 500,
            'buy_price' => 20000,
            'expired_date' => Carbon::now()->addYears(2),
        ]);

        $apAcc = Account::where('code', '2-1100')->first();
        $service->createJournalEntry([
            'date' => Carbon::now()->startOfMonth()->addDay(),
            'description' => 'Audit Scenario: Purchase (Partial Cash & Credit)',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $inventoryAcc->id, 'debit' => 10000000, 'credit' => 0],
                ['account_id' => $mandiri->id, 'debit' => 0, 'credit' => 5000000],
                ['account_id' => $apAcc->id, 'debit' => 0, 'credit' => 5000000],
            ]
        ]);

        // 6. SCENARIO 3: AP Payment (Rp 3M via BRI)
        $service->createJournalEntry([
            'date' => Carbon::now()->startOfMonth()->addDays(2),
            'description' => 'Audit Scenario: AP Payment',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $apAcc->id, 'debit' => 3000000, 'credit' => 0],
                ['account_id' => $bri->id, 'debit' => 0, 'credit' => 3000000],
            ]
        ]);

        // 7. SCENARIO 4: Sales (Rp 20M: 10M Cash, 10M BCA) + COGS (600 units @ Rp 20,000 = 12M)
        // Reduce stock from batches
        $openingBatch->update(['stock_current' => 1000 - 100]); // Sell 100 from opening
        $purchaseBatch->update(['stock_current' => 500 - 500]); // Sell all 500 from purchase

        $salesAcc = Account::where('code', '4-1000')->first();
        $cashAcc = Account::where('code', '1-1100')->first();
        $cogsAcc = Account::where('code', '5-1000')->first();
        
        $service->createJournalEntry([
            'date' => Carbon::now()->startOfMonth()->addDays(3),
            'description' => 'Audit Scenario: Sales',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $cashAcc->id, 'debit' => 10000000, 'credit' => 0],
                ['account_id' => $bca->id, 'debit' => 10000000, 'credit' => 0],
                ['account_id' => $salesAcc->id, 'debit' => 0, 'credit' => 20000000],
            ]
        ]);
        
        // COGS: 100 units @ 16,507.70 + 500 units @ 20,000 = 1,650,770 + 10,000,000 = 11,650,770
        $cogsAmount = (100 * 16507.70) + (500 * 20000);
        $service->createJournalEntry([
            'date' => Carbon::now()->startOfMonth()->addDays(3),
            'description' => 'Audit Scenario: COGS for Sales',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $cogsAcc->id, 'debit' => $cogsAmount, 'credit' => 0],
                ['account_id' => $inventoryAcc->id, 'debit' => 0, 'credit' => $cogsAmount],
            ]
        ]);

        // 8. SCENARIO 5: Operating Expenses (Rp 2M via BNI)
        $opexAcc = Account::where('code', '5-2100')->first();
        $service->createJournalEntry([
            'date' => Carbon::now()->startOfMonth()->addDays(4),
            'description' => 'Audit Scenario: Operating Expenses',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $opexAcc->id, 'debit' => 2000000, 'credit' => 0],
                ['account_id' => $bni->id, 'debit' => 0, 'credit' => 2000000],
            ]
        ]);

        // 9. SCENARIO 6: Fixed Asset Purchase & Depreciation
        $assetAcc = Account::where('code', '1-3400')->first();
        $accumulatedAcc = Account::where('code', '1-3410')->first();
        $depreciationExpAcc = Account::where('code', '5-2400')->first();
        
        // Purchase (Rp 15M via BCA)
        $service->createJournalEntry([
            'date' => Carbon::now()->startOfMonth()->addDays(5),
            'description' => 'Audit Scenario: Fixed Asset Purchase',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $assetAcc->id, 'debit' => 15000000, 'credit' => 0],
                ['account_id' => $bca->id, 'debit' => 0, 'credit' => 15000000],
            ]
        ]);

        // Manual Depreciation Entry (1 month)
        $service->createJournalEntry([
            'date' => Carbon::now()->endOfMonth(),
            'description' => 'Audit Scenario: Fixed Asset Depreciation',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $depreciationExpAcc->id, 'debit' => 312500, 'credit' => 0],
                ['account_id' => $accumulatedAcc->id, 'debit' => 0, 'credit' => 312500],
            ]
        ]);

        // 10. SCENARIO 7: Bank Transfer (5M Mandiri to BCA)
        $service->createJournalEntry([
            'date' => Carbon::now()->startOfMonth()->addDays(6),
            'description' => 'Audit Scenario: Inter-bank Transfer',
            'auto_post' => true,
            'lines' => [
                ['account_id' => $bca->id, 'debit' => 5000000, 'credit' => 0],
                ['account_id' => $mandiri->id, 'debit' => 0, 'credit' => 5000000],
            ]
        ]);

        $this->command->info('AI Audit simulation completed successfully.');
        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info('- Opening Inventory: 1,000 units @ Rp 16,507.70 = Rp 16,507,700');
        $this->command->info('- Purchase: 500 units @ Rp 20,000 = Rp 10,000,000');
        $this->command->info('- Sales: 600 units (100 + 500)');
        $this->command->info('- COGS: Rp ' . number_format($cogsAmount, 0, ',', '.'));
        $this->command->info('- Ending Inventory: 900 units @ Rp 16,507.70 = Rp ' . number_format(900 * 16507.70, 0, ',', '.'));
        $this->command->info('');
        $this->command->info('Run: php quick_audit.php to verify balance sheet');
    }
}
