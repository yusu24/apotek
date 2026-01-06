<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Sale;
use App\Models\Batch;
use App\Models\GoodsReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountingService
{
    /**
     * Create manual journal entry
     */
    public function createJournalEntry(array $data): JournalEntry
    {
        DB::beginTransaction();
        try {
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => $data['date'] ?? now(),
                'description' => $data['description'],
                'source' => 'manual',
                'user_id' => Auth::id() ?? 1,
            ]);

            // Create lines
            foreach ($data['lines'] as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'notes' => $line['notes'] ?? null,
                ]);
            }

            // Auto-post if requested
            if ($data['auto_post'] ?? false) {
                $entry->post();
            }

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Post sale journal automatically
     * 
     * Journal Entry:
     * Dr. Kas/Bank     [grand_total]
     *    Cr. Penjualan                [grand_total]
     * 
     * Dr. COGS         [cost_total]
     *    Cr. Persediaan               [cost_total]
     */
    public function postSaleJournal(int $saleId): ?JournalEntry
    {
        $sale = Sale::with('saleItems.batch')->findOrFail($saleId);
        
        // Check if journal already exists
        if (JournalEntry::where('source', 'sale')->where('source_id', $saleId)->exists()) {
            return null; // Already posted
        }

        DB::beginTransaction();
        try {
            // Get accounts
            $cashAccount = Account::where('code', '1-1100')->first(); // Kas
            $salesAccount = Account::where('code', '4-1000')->first(); // Penjualan
            $cogsAccount = Account::where('code', '5-1000')->first(); // COGS
            $inventoryAccount = Account::where('code', '1-1400')->first(); // Persediaan

            if (!$cashAccount || !$salesAccount || !$cogsAccount || !$inventoryAccount) {
                $missing = [];
                if (!$cashAccount) $missing[] = "Kas (1-1100)";
                if (!$salesAccount) $missing[] = "Penjualan (4-1000)";
                if (!$cogsAccount) $missing[] = "COGS (5-1000)";
                if (!$inventoryAccount) $missing[] = "Persediaan (1-1400)";
                
                throw new \Exception("Akun akuntansi berikut tidak ditemukan: " . implode(', ', $missing) . ". Silakan jalankan 'php artisan db:seed --class=AccountSeeder'.");
            }

            // Calculate COGS (cost of goods sold)
            $cogsTotal = 0;
            foreach ($sale->saleItems as $item) {
                if ($item->batch) {
                    $cogsTotal += $item->quantity * $item->batch->buy_price;
                }
            }

            // Create journal entry
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => $sale->date,
                'description' => 'Penjualan - ' . $sale->invoice_no,
                'source' => 'sale',
                'source_id' => $saleId,
                'user_id' => $sale->user_id,
            ]);

            // Entry 1: Record Sale Revenue
            // Dr. Kas
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $cashAccount->id,
                'debit' => $sale->grand_total,
                'credit' => 0,
                'notes' => 'Penjualan ' . $sale->invoice_no,
            ]);

            // Cr. Penjualan
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $sale->grand_total,
                'notes' => 'Penjualan ' . $sale->invoice_no,
            ]);

            // Entry 2: Record COGS
            if ($cogsTotal > 0) {
                // Dr. COGS
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $cogsAccount->id,
                    'debit' => $cogsTotal,
                    'credit' => 0,
                    'notes' => 'COGS - ' . $sale->invoice_no,
                ]);

                // Cr. Persediaan
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $cogsTotal,
                    'notes' => 'Penjualan Persediaan - ' . $sale->invoice_no,
                ]);
            }

            // Post journal
            $entry->post();

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Post purchase journal automatically
     * 
     * Journal Entry:
     * Dr. Persediaan   [total_amount]
     *    Cr. Kas/Bank/Utang          [total_amount]
     */
    public function postPurchaseJournal(int $goodsReceiptId): ?JournalEntry
    {
        $goodsReceipt = GoodsReceipt::with('items')->findOrFail($goodsReceiptId);
        
        // Check if journal already exists
        if (JournalEntry::where('source', 'purchase')->where('source_id', $goodsReceiptId)->exists()) {
            return null; // Already posted
        }

        DB::beginTransaction();
        try {
            // Get accounts
            $inventoryAccount = Account::where('code', '1-1400')->first(); // Persediaan
            
            // Determine payment account based on payment method
            $paymentAccount = match($goodsReceipt->payment_method) {
                'cash' => Account::where('code', '1-1100')->first(), // Kas
                'transfer' => Account::where('code', '1-1200')->first(), // Bank
                'due_date' => Account::where('code', '2-1200')->first(), // Utang Jatuh Tempo
                default => Account::where('code', '1-1100')->first(),
            };

            if (!$inventoryAccount || !$paymentAccount) {
                $missing = [];
                if (!$inventoryAccount) $missing[] = "Persediaan (1-1400)";
                if (!$paymentAccount) $missing[] = "Akun Pembayaran (" . ($goodsReceipt->payment_method ?? 'cash') . ")";
                
                throw new \Exception("Akun akuntansi berikut tidak ditemukan: " . implode(', ', $missing) . ". Silakan jalankan 'php artisan db:seed --class=AccountSeeder'.");
            }

            // Calculate total purchase
            $totalPurchase = $goodsReceipt->items->sum(function($item) {
                return $item->qty_received * $item->buy_price;
            });

            // Create journal entry
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => $goodsReceipt->received_date,
                'description' => 'Pembelian - ' . $goodsReceipt->delivery_note_number,
                'source' => 'purchase',
                'source_id' => $goodsReceiptId,
                'user_id' => $goodsReceipt->user_id ?? Auth::id(),
            ]);

            // Dr. Persediaan
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $inventoryAccount->id,
                'debit' => $totalPurchase,
                'credit' => 0,
                'notes' => 'Pembelian - ' . $goodsReceipt->delivery_note_number,
            ]);

            // Cr. Kas/Bank/Utang
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $paymentAccount->id,
                'debit' => 0,
                'credit' => $totalPurchase,
                'notes' => 'Pembayaran Pembelian - ' . $goodsReceipt->delivery_note_number,
            ]);

            // Post journal
            $entry->post();

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Post supplier payment journal automatically
     * 
     * Journal Entry:
     * Dr. Utang Jatuh Tempo [amount]
     *    Cr. Kas/Bank            [amount]
     */
    public function postSupplierPaymentJournal(int $paymentId): ?JournalEntry
    {
        $payment = \App\Models\SupplierPayment::with('goodsReceipt')->findOrFail($paymentId);
        
        // Check if journal already exists
        if (JournalEntry::where('source', 'supplier_payment')->where('source_id', $paymentId)->exists()) {
            return null; // Already posted
        }

        DB::beginTransaction();
        try {
            // Get accounts
            $payableAccount = Account::where('code', '2-1200')->first(); // Utang Jatuh Tempo
            
            $paymentAccount = match($payment->payment_method) {
                'cash' => Account::where('code', '1-1100')->first(), // Kas
                'transfer' => Account::where('code', '1-1200')->first(), // Bank
                default => Account::where('code', '1-1100')->first(),
            };

            if (!$payableAccount || !$paymentAccount) {
                $missing = [];
                if (!$payableAccount) $missing[] = "Utang Jatuh Tempo (2-1200)";
                if (!$paymentAccount) $missing[] = "Akun Pembayaran (" . ($payment->payment_method ?? 'cash') . ")";
                
                throw new \Exception("Akun akuntansi berikut tidak ditemukan: " . implode(', ', $missing));
            }

            // Create journal entry
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => $payment->payment_date,
                'description' => 'Pelunasan Hutang - SJ: ' . ($payment->goodsReceipt->delivery_note_number ?? '-'),
                'source' => 'supplier_payment',
                'source_id' => $paymentId,
                'user_id' => $payment->user_id ?? Auth::id(),
            ]);

            // Dr. Utang Jatuh Tempo
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $payableAccount->id,
                'debit' => $payment->amount,
                'credit' => 0,
                'notes' => 'Pelunasan SJ: ' . $payment->goodsReceipt->delivery_note_number,
            ]);

            // Cr. Kas/Bank
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $paymentAccount->id,
                'debit' => 0,
                'credit' => $payment->amount,
                'notes' => 'Pelunasan SJ: ' . $payment->goodsReceipt->delivery_note_number,
            ]);

            // Post journal
            $entry->post();

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Post expense journal automatically
     * 
     * Journal Entry:
     * Dr. Beban (Category Account) [amount]
     *    Cr. Kas/Bank/Utang          [amount]
     */
    public function postExpenseJournal(int $expenseId, int $accountId): ?JournalEntry
    {
        $expense = \App\Models\Expense::findOrFail($expenseId);
        $paymentAccount = Account::findOrFail($accountId);
        
        // Find expense account based on category or default to general expense
        $expenseAccount = Account::where('name', 'like', '%' . $expense->category . '%')
            ->where('type', 'expense')
            ->first() ?? Account::where('code', '5-2300')->first(); // Default Beban Operasional Lainnya

        if (!$expenseAccount) {
            throw new \Exception("Akun Beban tidak ditemukan. Silakan buat akun beban terlebih dahulu.");
        }

        DB::beginTransaction();
        try {
            // Create journal entry
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => $expense->date,
                'description' => 'Pengeluaran - ' . $expense->description,
                'source' => 'expense',
                'source_id' => $expenseId,
                'user_id' => $expense->user_id ?? Auth::id(),
            ]);

            // Dr. Beban
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $expenseAccount->id,
                'debit' => $expense->amount,
                'credit' => 0,
                'notes' => $expense->description,
            ]);

            // Cr. Kas/Bank/Utang
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $paymentAccount->id,
                'debit' => 0,
                'credit' => $expense->amount,
                'notes' => 'Pembayaran: ' . $expense->description,
            ]);

            // Post journal
            $entry->post();

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculate and update inventory value (FIFO based on batches)
     */
    public function calculateInventoryValue(): float
    {
        $totalValue = 0;
        
        // Get all batches with remaining stock
        $batches = Batch::where('stock_current', '>', 0)->get();
        
        foreach ($batches as $batch) {
            $value = $batch->stock_current * $batch->buy_price;
            $totalValue += $value;
        }
        
        // Update Persediaan account balance
        $inventoryAccount = Account::where('code', '1-1400')->first();
        if ($inventoryAccount) {
            $inventoryAccount->update(['balance' => $totalValue]);
        }
        
        return $totalValue;
    }

    /**
     * Generate Balance Sheet
     */
    public function getBalanceSheet($startDate = null, $endDate = null): array
    {
        // Calculate inventory value first
        $this->calculateInventoryValue();
        
        // Get all accounts grouped by type and category
        $assets = Account::where('type', 'asset')->orderBy('code')->get();
        $liabilities = Account::where('type', 'liability')->orderBy('code')->get();
        $equity = Account::where('type', 'equity')->orderBy('code')->get();
        
        // Group assets
        $currentAssets = $assets->where('category', 'current_asset');
        $fixedAssets = $assets->where('category', 'fixed_asset');
        
        // Group liabilities
        $currentLiabilities = $liabilities->where('category', 'current_liability');
        $longTermLiabilities = $liabilities->where('category', 'long_term_liability');
        
        // Calculate totals
        $totalCurrentAssets = $currentAssets->sum('balance');
        $totalFixedAssets = $fixedAssets->sum('balance');
        $totalAssets = $totalCurrentAssets + $totalFixedAssets;
        
        $totalCurrentLiabilities = $currentLiabilities->sum('balance');
        $totalLongTermLiabilities = $longTermLiabilities->sum('balance');
        $totalLiabilities = $totalCurrentLiabilities + $totalLongTermLiabilities;
        
        $totalEquity = $equity->sum('balance');
        
        // Calculate net income for the period (to add to retained earnings)
        $incomeStatement = $this->getIncomeStatement($startDate, $endDate);
        $netIncome = $incomeStatement['net_income'];
        
        // Balance check
        $balanceCheck = abs($totalAssets - ($totalLiabilities + $totalEquity + $netIncome)) < 0.01;
        
        return [
            'current_assets' => $currentAssets,
            'fixed_assets' => $fixedAssets,
            'total_current_assets' => $totalCurrentAssets,
            'total_fixed_assets' => $totalFixedAssets,
            'total_assets' => $totalAssets,
            
            'current_liabilities' => $currentLiabilities,
            'long_term_liabilities' => $longTermLiabilities,
            'total_current_liabilities' => $totalCurrentLiabilities,
            'total_long_term_liabilities' => $totalLongTermLiabilities,
            'total_liabilities' => $totalLiabilities,
            
            'equity' => $equity,
            'total_equity' => $totalEquity,
            'net_income' => $netIncome,
            
            'balance_check' => $balanceCheck,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Generate Income Statement
     */
    public function getIncomeStatement($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();
        
        // Get revenue and expense accounts
        $revenueAccounts = Account::where('type', 'revenue')->orderBy('code')->get();
        $expenseAccounts = Account::where('type', 'expense')->orderBy('code')->get();
        
        // Separate COGS from other expenses
        $cogsAccounts = $expenseAccounts->where('category', 'cogs');
        $operatingExpenses = $expenseAccounts->where('category', 'operating_expense');
        $otherExpenses = $expenseAccounts->where('category', 'other');
        
        // Calculate totals
        $totalRevenue = $revenueAccounts->sum('balance');
        $totalCOGS = $cogsAccounts->sum('balance');
        $grossProfit = $totalRevenue - $totalCOGS;
        
        $totalOperatingExpenses = $operatingExpenses->sum('balance');
        $totalOtherExpenses = $otherExpenses->sum('balance');
        $totalExpenses = $totalOperatingExpenses + $totalOtherExpenses;
        
        $netIncome = $grossProfit - $totalExpenses;
        
        return [
            'revenue_accounts' => $revenueAccounts,
            'total_revenue' => $totalRevenue,
            
            'cogs_accounts' => $cogsAccounts,
            'total_cogs' => $totalCOGS,
            'gross_profit' => $grossProfit,
            
            'operating_expense_accounts' => $operatingExpenses,
            'other_expense_accounts' => $otherExpenses,
            'total_operating_expenses' => $totalOperatingExpenses,
            'total_other_expenses' => $totalOtherExpenses,
            'total_expenses' => $totalExpenses,
            
            'net_income' => $netIncome,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Get Trial Balance
     */
    public function getTrialBalance($date = null): array
    {
        $date = $date ?? now();
        
        $accounts = Account::where('is_active', true)->orderBy('code')->get();
        
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($accounts as $account) {
            if ($account->balance >= 0) {
                $totalDebit += $account->balance;
            } else {
                $totalCredit += abs($account->balance);
            }
        }
        
        $isBalanced = abs($totalDebit - $totalCredit) < 0.01;
        
        return [
            'accounts' => $accounts,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => $isBalanced,
            'date' => $date,
        ];
    }
}
