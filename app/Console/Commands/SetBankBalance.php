<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;

class SetBankBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:set-bank-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set opening balance for a bank account via Journal Entry';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Bank Opening Balance Setup...');

        // 1. Select Bank Account
        $accounts = Account::where('category', 'cash_bank')->get();
        
        if ($accounts->isEmpty()) {
            $this->error('No bank accounts found!');
            return;
        }

        $headers = ['ID', 'Code', 'Name', 'Current Balance'];
        $data = $accounts->map(function($acc) {
            return [
                $acc->id,
                $acc->code,
                $acc->name,
                number_format($acc->balance, 0, ',', '.')
            ];
        })->toArray();

        $this->table($headers, $data);

        $bankAccountId = $this->ask('Enter the ID of the Bank Account to set balance for');
        $bankAccount = Account::find($bankAccountId);

        if (!$bankAccount) {
            $this->error('Invalid Account ID!');
            return;
        }

        // 2. Input Amount
        $amount = $this->ask('Enter Opening Balance Amount (e.g. 100000000)');
        
        // Cleanup non-numeric characters
        $amount = (float) preg_replace('/[^0-9]/', '', $amount);

        if ($amount <= 0) {
            $this->error('Amount must be greater than 0');
            return;
        }

        // 3. Find Equity Account (Modal Awal)
        // Try to find "Modal Awal" or "Opening Balance Equity" or general Equity
        $equityAccount = Account::where('name', 'like', '%Modal Awal%')
            ->orWhere('name', 'like', '%Opening Balance%')
            ->orWhere('type', 'equity')
            ->first();

        if (!$equityAccount) {
            // Fallback: Create one if needed, or ask user to create it first.
            // For now let's just use the first equity account we found earlier or fail
            $this->error('No Equity/Modal account found to balance the transaction!');
            return;
        }

        $this->info("Selected Bank: {$bankAccount->name} ({$bankAccount->code})");
        $this->info("Amount to Debit: Rp " . number_format($amount, 0, ',', '.'));
        $this->info("Contra Account (Credit): {$equityAccount->name} ({$equityAccount->code})");

        if (!$this->confirm('Do you wish to proceed?', true)) {
            $this->info('Operation cancelled.');
            return;
        }

        DB::transaction(function () use ($bankAccount, $equityAccount, $amount) {
            // Create Journal Entry
            // Create Journal Entry
            $journal = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => now(),
                'description' => 'Saldo Awal Bank ' . $bankAccount->name,
                'user_id' => 1, // System/Admin
                'is_posted' => true,
            ]);

            // Debit Bank
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $bankAccount->id,
                'debit' => $amount,
                'credit' => 0,
            ]);
            $bankAccount->updateBalance($amount, 'debit');

            // Credit Equity
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $equityAccount->id,
                'debit' => 0,
                'credit' => $amount,
            ]);
            $equityAccount->updateBalance($amount, 'credit');
        });

        $this->info('Successfully set opening balance!');
        $this->info("New Balance for {$bankAccount->name}: Rp " . number_format($bankAccount->fresh()->balance, 0, ',', '.'));
    }
}
