<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;

class AccountingSampleSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        try {
            // 1. Create Bank Accounts
            $banks = [
                ['code' => '1-1201', 'name' => 'Bank Mandiri', 'category' => 'current_asset'],
                ['code' => '1-1202', 'name' => 'Bank BCA', 'category' => 'current_asset'],
                ['code' => '1-1203', 'name' => 'Bank BRI', 'category' => 'current_asset'],
                ['code' => '1-1204', 'name' => 'Bank BNI', 'category' => 'current_asset'],
                ['code' => '1-1205', 'name' => 'Bank BTPN', 'category' => 'current_asset'],
            ];

            foreach ($banks as $bank) {
                Account::updateOrCreate(
                    ['code' => $bank['code']],
                    [
                        'name' => $bank['name'],
                        'type' => 'asset',
                        'category' => $bank['category'],
                        'is_active' => true,
                        'is_system' => false,
                    ]
                );
            }

            // 2. Create Liability Account (Bank Loan)
            $loanAccount = Account::updateOrCreate(
                ['code' => '2-2100'],
                [
                    'name' => 'Hutang Bank (Pinjaman Modal)',
                    'type' => 'liability',
                    'category' => 'long_term_liability',
                    'is_active' => true,
                    'is_system' => false,
                ]
            );

            // 3. Create Equity Accounts (Investors)
            $investor1 = Account::updateOrCreate(
                ['code' => '3-1100'],
                [
                    'name' => 'Modal Investor 1',
                    'type' => 'equity',
                    'category' => 'capital',
                    'is_active' => true,
                    'is_system' => false,
                ]
            );

            $investor2 = Account::updateOrCreate(
                ['code' => '3-1200'],
                [
                    'name' => 'Modal Investor 2',
                    'type' => 'equity',
                    'category' => 'capital',
                    'is_active' => true,
                    'is_system' => false,
                ]
            );

            // 4. Create Balancing Journal Entry
            $accountingService = new AccountingService();
            
            $data = [
                'date' => now()->startOfMonth(),
                'description' => 'Saldo Awal - Modal Investor & Pinjaman Bank',
                'auto_post' => true,
                'lines' => [
                    // Debits (Assets)
                    ['account_id' => Account::where('code', '1-1201')->first()->id, 'debit' => 200000000, 'credit' => 0, 'notes' => 'Setoran Modal ke Mandiri'],
                    ['account_id' => Account::where('code', '1-1202')->first()->id, 'debit' => 150000000, 'credit' => 0, 'notes' => 'Setoran Modal ke BCA'],
                    ['account_id' => Account::where('code', '1-1203')->first()->id, 'debit' => 100000000, 'credit' => 0, 'notes' => 'Setoran Modal ke BRI'],
                    ['account_id' => Account::where('code', '1-1204')->first()->id, 'debit' => 75000000, 'credit' => 0, 'notes' => 'Setoran Modal ke BNI'],
                    ['account_id' => Account::where('code', '1-1205')->first()->id, 'debit' => 25000000, 'credit' => 0, 'notes' => 'Setoran Modal ke BTPN'],
                    
                    // Credits (Liabilities & Equity)
                    ['account_id' => $loanAccount->id, 'debit' => 0, 'credit' => 250000000, 'notes' => 'Pinjaman Bank'],
                    ['account_id' => $investor1->id, 'debit' => 0, 'credit' => 150000000, 'notes' => 'Modal Investor 1'],
                    ['account_id' => $investor2->id, 'debit' => 0, 'credit' => 150000000, 'notes' => 'Modal Investor 2'],
                ]
            ];

            $accountingService->createJournalEntry($data);

            DB::commit();
            $this->command->info('Accounting Sample Data seeded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeding failed: ' . $e->getMessage());
        }
    }
}
