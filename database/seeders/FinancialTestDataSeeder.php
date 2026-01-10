<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Unit;
use App\Models\GoodsReceipt;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Account;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $accountingService = new AccountingService();
        $user = \App\Models\User::first();
        if (!$user) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }
        $userId = $user->id;

        // 1. Ensure basic data exists
        $category = Category::firstOrCreate(['name' => 'Obat Bebas']);
        $unit = Unit::firstOrCreate(['name' => 'Botol'], ['short_name' => 'Btl']);

        $supplier1 = Supplier::firstOrCreate(['name' => 'Kimia Farma Trading'], ['contact_person' => 'Budi', 'phone' => '08123456789']);
        $supplier2 = Supplier::firstOrCreate(['name' => 'Enseval Putera Megatrading'], ['contact_person' => 'Dewi', 'phone' => '08987654321']);

        $product1 = Product::firstOrCreate(['barcode' => 'FIN-TEST-PCT-001'], [
            'name' => 'Test Paracetamol',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'slug' => \Illuminate\Support\Str::slug('Test Paracetamol ' . time()),
            'sell_price' => 18500,
        ]);

        $product2 = Product::firstOrCreate(['barcode' => 'FIN-TEST-AMX-001'], [
            'name' => 'Test Amoxicillin',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'slug' => \Illuminate\Support\Str::slug('Test Amoxicillin ' . time()),
            'sell_price' => 15000,
        ]);

        // 2. Create Goods Receipts (Last Month & This Month)
        // Receipt 1: Paid by Cash (Last Month)
        $date1 = Carbon::now()->subMonth()->startOfMonth()->addDays(5);
        $gr1 = GoodsReceipt::firstOrCreate(
            ['delivery_note_number' => 'SJ/2023/X/001'],
            [
                'received_date' => $date1->format('Y-m-d'),
                'user_id' => $userId,
                'payment_method' => 'cash',
                'total_amount' => 1500000,
                'paid_amount' => 1500000,
                'payment_status' => 'paid',
            ]
        );
        if ($gr1->wasRecentlyCreated) {
            $gr1->items()->create(['product_id' => $product1->id, 'qty_received' => 100, 'buy_price' => 15000, 'batch_no' => 'B1', 'expired_date' => $date1->copy()->addYear()]);
            $accountingService->postPurchaseJournal($gr1->id);
        }

        // Receipt 2: Pending/Debt (This Month)
        $date2 = Carbon::now()->startOfMonth()->addDays(2);
        $gr2 = GoodsReceipt::firstOrCreate(
            ['delivery_note_number' => 'SJ/2023/XI/012'],
            [
                'received_date' => $date2->format('Y-m-d'),
                'user_id' => $userId,
                'purchase_order_id' => null,
                'payment_method' => 'due_date',
                'total_amount' => 2400000,
                'paid_amount' => 0,
                'payment_status' => 'pending',
                'due_date' => $date2->copy()->addWeeks(4),
            ]
        );
        if ($gr2->wasRecentlyCreated) {
            $gr2->items()->create(['product_id' => $product2->id, 'qty_received' => 200, 'buy_price' => 12000, 'batch_no' => 'B2', 'expired_date' => $date2->copy()->addYear()]);
            $accountingService->postPurchaseJournal($gr2->id);
        }

        // 3. Create Sales (Last Month & This Month)
        // Sale 1: Cash (Last Month)
        $sDate1 = Carbon::now()->subMonth()->startOfMonth()->addDays(10);
        $sale1 = Sale::firstOrCreate(
            ['invoice_no' => 'INV/' . $sDate1->format('Ymd') . '/S001'],
            [
                'date' => $sDate1,
                'total_amount' => 500000,
                'grand_total' => 500000,
                'payment_method' => 'cash',
                'user_id' => $userId,
                'status' => 'completed',
            ]
        );
        if ($sale1->wasRecentlyCreated) {
            $sale1->saleItems()->create(['product_id' => $product1->id, 'quantity' => 10, 'sell_price' => 18500, 'subtotal' => 185000]);
            $accountingService->postSaleJournal($sale1->id);
        }

        // Sale 2: Transfer (This Month)
        $sDate2 = Carbon::now()->startOfMonth()->addDays(5);
        $sale2 = Sale::firstOrCreate(
            ['invoice_no' => 'INV/' . $sDate2->format('Ymd') . '/S022'],
            [
                'date' => $sDate2,
                'total_amount' => 750000,
                'grand_total' => 750000,
                'payment_method' => 'transfer',
                'user_id' => $userId,
                'status' => 'completed',
            ]
        );
        if ($sale2->wasRecentlyCreated) {
            $sale2->saleItems()->create(['product_id' => $product2->id, 'quantity' => 50, 'sell_price' => 15000, 'subtotal' => 750000]);
            $accountingService->postSaleJournal($sale2->id);
        }

        // 4. Create Expenses
        $eDate1 = Carbon::now()->startOfMonth()->addDays(1);
        $expenseAccount = Account::where('code', '1-1100')->first(); // Kas
        if ($expenseAccount) {
            $expense = Expense::firstOrCreate(
                ['description' => 'Bayar Listrik Kantor', 'date' => $eDate1->format('Y-m-d')],
                [
                    'amount' => 350000,
                    'category' => 'Listrik',
                    'account_id' => $expenseAccount->id,
                    'user_id' => $userId,
                ]
            );
            if ($expense->wasRecentlyCreated) {
                $accountingService->postExpenseJournal($expense->id, $expenseAccount->id);
            }
        }
    }
}
