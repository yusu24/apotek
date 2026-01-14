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
     * Post sales return journal automatically
     * 
     * Journal Entry:
     * Dr. Retur Penjualan (Contra Revenue)  [total_amount]
     *    Cr. Kas/Bank                              [total_amount]
     * 
     * Dr. Persediaan                        [cost_total]
     *    Cr. COGS                                  [cost_total]
     */
    public function postSalesReturnJournal(int $salesReturnId): ?JournalEntry
    {
        $salesReturn = \App\Models\SalesReturn::with(['items.product', 'sale'])->findOrFail($salesReturnId);
        
        // Check if journal already exists
        if (JournalEntry::where('source', 'sales_return')->where('source_id', $salesReturnId)->exists()) {
            return null; // Already posted
        }

        DB::beginTransaction();
        try {
            // Get accounts
            $cashAccount = Account::where('code', '1-1100')->first(); // Kas
            $salesReturnAccount = Account::where('code', '4-1100')->first(); // Retur Penjualan
            $cogsAccount = Account::where('code', '5-1000')->first(); // COGS
            $inventoryAccount = Account::where('code', '1-1400')->first(); // Persediaan

            if (!$cashAccount || !$salesReturnAccount || !$cogsAccount || !$inventoryAccount) {
                $missing = [];
                if (!$cashAccount) $missing[] = "Kas (1-1100)";
                if (!$salesReturnAccount) $missing[] = "Retur Penjualan (4-1100)";
                if (!$cogsAccount) $missing[] = "COGS (5-1000)";
                if (!$inventoryAccount) $missing[] = "Persediaan (1-1400)";
                
                throw new \Exception("Akun akuntansi berikut tidak ditemukan: " . implode(', ', $missing) . ". Silakan jalankan 'php artisan db:seed --class=AccountSeeder'.");
            }

            // Calculate COGS (cost to restore)
            $cogsTotal = 0;
            foreach ($salesReturn->items as $item) {
                // Get the original batch cost from the sale item
                $saleItem = \App\Models\SaleItem::where('sale_id', $salesReturn->sale_id)
                    ->where('product_id', $item->product_id)
                    ->first();
                
                if ($saleItem && $saleItem->batch) {
                    $cogsTotal += $item->quantity * $saleItem->batch->buy_price;
                }
            }

            // Create journal entry
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => $salesReturn->created_at ?? now(),
                'description' => 'Retur Penjualan - ' . $salesReturn->return_no,
                'source' => 'sales_return',
                'source_id' => $salesReturnId,
                'user_id' => $salesReturn->user_id,
            ]);

            // Entry 1: Record Sales Return (Reduce Revenue)
            // Dr. Retur Penjualan (Contra Revenue)
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $salesReturnAccount->id,
                'debit' => $salesReturn->total_amount,
                'credit' => 0,
                'notes' => 'Retur Penjualan ' . $salesReturn->return_no,
            ]);

            // Cr. Kas
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $salesReturn->total_amount,
                'notes' => 'Pengembalian Dana - ' . $salesReturn->return_no,
            ]);

            // Entry 2: Restore Inventory
            if ($cogsTotal > 0) {
                // Dr. Persediaan
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $inventoryAccount->id,
                    'debit' => $cogsTotal,
                    'credit' => 0,
                    'notes' => 'Pengembalian Persediaan - ' . $salesReturn->return_no,
                ]);

                // Cr. COGS
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $cogsAccount->id,
                    'debit' => 0,
                    'credit' => $cogsTotal,
                    'notes' => 'Pengurangan COGS - ' . $salesReturn->return_no,
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
     * Post purchase return journal automatically
     * 
     * Journal Entry:
     * Dr. Kas/Bank                          [total_amount]
     *    Cr. Retur Pembelian                      [total_amount]
     * 
     * Dr. COGS                              [cost_total]
     *    Cr. Persediaan                           [cost_total]
     */
    public function postPurchaseReturnJournal(int $purchaseReturnId): ?JournalEntry
    {
        $purchaseReturn = \App\Models\PurchaseReturn::with(['items.product', 'supplier'])->findOrFail($purchaseReturnId);
        
        // Check if journal already exists
        if (JournalEntry::where('source', 'purchase_return')->where('source_id', $purchaseReturnId)->exists()) {
            return null; // Already posted
        }

        DB::beginTransaction();
        try {
            // Get accounts
            $cashAccount = Account::where('code', '1-1100')->first(); // Kas
            $purchaseReturnAccount = Account::where('code', '5-1100')->first(); // Retur Pembelian
            $cogsAccount = Account::where('code', '5-1000')->first(); // COGS
            $inventoryAccount = Account::where('code', '1-1400')->first(); // Persediaan

            if (!$cashAccount || !$purchaseReturnAccount || !$cogsAccount || !$inventoryAccount) {
                $missing = [];
                if (!$cashAccount) $missing[] = "Kas (1-1100)";
                if (!$purchaseReturnAccount) $missing[] = "Retur Pembelian (5-1100)";
                if (!$cogsAccount) $missing[] = "COGS (5-1000)";
                if (!$inventoryAccount) $missing[] = "Persediaan (1-1400)";
                
                throw new \Exception("Akun akuntansi berikut tidak ditemukan: " . implode(', ', $missing) . ". Silakan jalankan 'php artisan db:seed --class=AccountSeeder'.");
            }

            // Calculate total cost
            $costTotal = 0;
            foreach ($purchaseReturn->items as $item) {
                // Use the buy price from the batch
                if ($item->batch) {
                    $costTotal += $item->quantity * $item->batch->buy_price;
                }
            }

            // Create journal entry
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => $purchaseReturn->created_at ?? now(),
                'description' => 'Retur Pembelian - ' . $purchaseReturn->return_no,
                'source' => 'purchase_return',
                'source_id' => $purchaseReturnId,
                'user_id' => $purchaseReturn->user_id,
            ]);

            // Entry 1: Record Cash Refund
            // Dr. Kas
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $cashAccount->id,
                'debit' => $purchaseReturn->total_amount,
                'credit' => 0,
                'notes' => 'Pengembalian Dana - ' . $purchaseReturn->return_no,
            ]);

            // Cr. Retur Pembelian
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $purchaseReturnAccount->id,
                'debit' => 0,
                'credit' => $purchaseReturn->total_amount,
                'notes' => 'Retur Pembelian - ' . $purchaseReturn->return_no,
            ]);

            // Entry 2: Reduce Inventory
            if ($costTotal > 0) {
                // Dr. COGS (expense the returned goods)
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $cogsAccount->id,
                    'debit' => $costTotal,
                    'credit' => 0,
                    'notes' => 'Beban Retur - ' . $purchaseReturn->return_no,
                ]);

                // Cr. Persediaan
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $costTotal,
                    'notes' => 'Pengurangan Persediaan - ' . $purchaseReturn->return_no,
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
     * Calculates debit/credit totals per account from journal entries within a date range
     */
    public function getTrialBalance($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $endDate ?? now()->endOfMonth()->format('Y-m-d');
        
        // Get all accounts with their journal entry totals
        $accountsData = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->where('journal_entries.is_posted', true)
            ->whereDate('journal_entries.date', '>=', $startDate)
            ->whereDate('journal_entries.date', '<=', $endDate)
            ->select(
                'accounts.id',
                'accounts.code',
                'accounts.name',
                'accounts.type',
                'accounts.category',
                DB::raw('SUM(journal_entry_lines.debit) as total_debit'),
                DB::raw('SUM(journal_entry_lines.credit) as total_credit')
            )
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type', 'accounts.category')
            ->orderBy('accounts.code')
            ->get();
        
        // Group by account type
        $assets = $accountsData->where('type', 'asset');
        $liabilities = $accountsData->where('type', 'liability');
        $equity = $accountsData->where('type', 'equity');
        $revenue = $accountsData->where('type', 'revenue');
        $expenses = $accountsData->where('type', 'expense');
        
        // Calculate totals per type
        $totalAssetsDebit = $assets->sum('total_debit');
        $totalAssetsCredit = $assets->sum('total_credit');
        
        $totalLiabilitiesDebit = $liabilities->sum('total_debit');
        $totalLiabilitiesCredit = $liabilities->sum('total_credit');
        
        $totalEquityDebit = $equity->sum('total_debit');
        $totalEquityCredit = $equity->sum('total_credit');
        
        $totalRevenueDebit = $revenue->sum('total_debit');
        $totalRevenueCredit = $revenue->sum('total_credit');
        
        $totalExpensesDebit = $expenses->sum('total_debit');
        $totalExpensesCredit = $expenses->sum('total_credit');
        
        // Calculate grand totals
        $grandTotalDebit = $accountsData->sum('total_debit');
        $grandTotalCredit = $accountsData->sum('total_credit');
        
        // Validate balance
        $difference = $grandTotalDebit - $grandTotalCredit;
        $isBalanced = abs($difference) < 0.01;
        
        return [
            'accounts' => $accountsData,
            
            // Grouped by type
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'revenue' => $revenue,
            'expenses' => $expenses,
            
            // Subtotals per type
            'total_assets_debit' => $totalAssetsDebit,
            'total_assets_credit' => $totalAssetsCredit,
            'total_liabilities_debit' => $totalLiabilitiesDebit,
            'total_liabilities_credit' => $totalLiabilitiesCredit,
            'total_equity_debit' => $totalEquityDebit,
            'total_equity_credit' => $totalEquityCredit,
            'total_revenue_debit' => $totalRevenueDebit,
            'total_revenue_credit' => $totalRevenueCredit,
            'total_expenses_debit' => $totalExpensesDebit,
            'total_expenses_credit' => $totalExpensesCredit,
            
            // Grand totals
            'grand_total_debit' => $grandTotalDebit,
            'grand_total_credit' => $grandTotalCredit,
            
            // Validation
            'is_balanced' => $isBalanced,
            'difference' => $difference,
            
            // Period
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Get AP Aging Report
     */
    public function getApAgingReport($includePaid = false)
    {
        $statuses = ['pending', 'partial'];
        if ($includePaid) {
            $statuses[] = 'paid';
        }

        $receipts = \App\Models\GoodsReceipt::with('purchaseOrder.supplier')
            ->whereIn('payment_status', $statuses)
            ->get();
            
        $agingData = [
            '0-30' => [],
            '31-60' => [],
            '61-90' => [],
            '>90' => [],
            'summary' => [
                '0-30' => 0,
                '31-60' => 0,
                '61-90' => 0,
                '>90' => 0,
                'total' => 0
            ]
        ];
        
        foreach ($receipts as $receipt) {
            $age = \Carbon\Carbon::parse($receipt->received_date)->diffInDays(now());
            $outstanding = $receipt->total_amount - $receipt->paid_amount;
            
            // Skip zero or negative outstanding UNLESS we want to show paid
            if (!$includePaid && $outstanding <= 0.01) continue;

            $item = [
                'id' => $receipt->id,
                'supplier' => $receipt->purchaseOrder->supplier->name ?? 'Unknown',
                'invoice_number' => $receipt->delivery_note_number,
                'date' => $receipt->received_date,
                'due_date' => $receipt->due_date,
                'age' => (int) $age,
                'outstanding' => $outstanding,
                'total_amount' => $receipt->total_amount,
                'status' => $receipt->payment_status
            ];
            
            if ($age <= 30) {
                $agingData['0-30'][] = $item;
                $agingData['summary']['0-30'] += $outstanding;
            } elseif ($age <= 60) {
                $agingData['31-60'][] = $item;
                $agingData['summary']['31-60'] += $outstanding;
            } elseif ($age <= 90) {
                $agingData['61-90'][] = $item;
                $agingData['summary']['61-90'] += $outstanding;
            } else {
                $agingData['>90'][] = $item;
                $agingData['summary']['>90'] += $outstanding;
            }
            
            $agingData['summary']['total'] += $outstanding;
        }
        
        // Sort each bucket by age descending (oldest first)
        foreach (['0-30', '31-60', '61-90', '>90'] as $key) {
            usort($agingData[$key], function($a, $b) {
                return $b['age'] <=> $a['age'];
            });
        }
        
        return $agingData;
    }

/**
 * Get AR Aging Report (Piutang)
 */
public function getArAgingReport($includePaid = false)
{
    $statuses = ['partial', 'unpaid'];
    if ($includePaid) {
        $statuses[] = 'paid';
    }

    $receivables = \App\Models\Receivable::with(['customer', 'sale'])
        ->whereIn('status', $statuses)
        ->get();
        
    $agingData = [
        '0-30' => [],
        '31-60' => [],
        '61-90' => [],
        '>90' => [],
        'summary' => [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '>90' => 0,
            'total' => 0
        ]
    ];
    
    foreach ($receivables as $receivable) {
        // Age based on created_at (transaction date)
        $age = $receivable->created_at->diffInDays(now());
        
        // Days until due (Positive = Remaining, Negative = Overdue)
        $daysRemaining = $receivable->due_date ? now()->diffInDays($receivable->due_date, false) : null;
        
        $outstanding = $receivable->remaining_balance;
        
        // Skip zero or negative outstanding UNLESS we want to show paid
        if (!$includePaid && $outstanding <= 0.01) continue;

        $item = [
            'id' => $receivable->id,
            'customer' => $receivable->customer->name ?? 'Unknown',
            'customer_phone' => $receivable->customer->phone ?? '-',
            'invoice_number' => $receivable->sale->invoice_no ?? '-',
            'date' => $receivable->created_at->format('Y-m-d'),
            'due_date' => $receivable->due_date ? $receivable->due_date->format('Y-m-d') : '-',
            'age' => (int) $age,
            'days_remaining' => $daysRemaining !== null ? (int) $daysRemaining : null,
            'outstanding' => $outstanding,
            'total_amount' => $receivable->amount,
            'paid_amount' => $receivable->paid_amount,
            'status' => $receivable->status
        ];
        
        if ($age <= 30) {
            $agingData['0-30'][] = $item;
            $agingData['summary']['0-30'] += $outstanding;
        } elseif ($age <= 60) {
            $agingData['31-60'][] = $item;
            $agingData['summary']['31-60'] += $outstanding;
        } elseif ($age <= 90) {
            $agingData['61-90'][] = $item;
            $agingData['summary']['61-90'] += $outstanding;
        } else {
            $agingData['>90'][] = $item;
            $agingData['summary']['>90'] += $outstanding;
        }
        
        $agingData['summary']['total'] += $outstanding;
    }
    
    // Sort each bucket by age descending (oldest first)
    foreach (['0-30', '31-60', '61-90', '>90'] as $key) {
        usort($agingData[$key], function($a, $b) {
            return $b['age'] <=> $a['age'];
        });
    }
    
    return $agingData;
}

/**
 * Process Receivable Payment
 */
public function processReceivablePayment($receivableId, $data)
{
    $receivable = \App\Models\Receivable::findOrFail($receivableId);
    
    if ($data['amount'] > $receivable->remaining_balance) {
        throw new \Exception('Jumlah pembayaran melebihi sisa hutang.');
    }

    \DB::beginTransaction();
    try {
        // 1. Create Payment Record
        $payment = \App\Models\ReceivablePayment::create([
            'receivable_id' => $receivable->id,
            'user_id' => auth()->id() ?? 1,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? 'cash',
            'notes' => $data['notes'] ?? null,
            'paid_at' => now(),
        ]);

        // 2. Update Receivable Balance
        $receivable->paid_amount += $data['amount'];
        $receivable->remaining_balance -= $data['amount'];
        
        if ($receivable->remaining_balance <= 0) {
            $receivable->status = 'paid';
            $receivable->remaining_balance = 0; // Ensure no negative zero
        } else {
            $receivable->status = 'partial';
        }
        $receivable->save();

        // 3. Create Journal Entry (Cash/Bank Debit, Accounts Receivable Credit)
        $paymentAccount = match($data['payment_method'] ?? 'cash') {
            'cash' => Account::where('code', '1-1100')->first(), // Kas
            'transfer' => Account::where('code', '1-1200')->first(), // Bank
            default => Account::where('code', '1-1100')->first(),
        };
        
        $receivableAccount = Account::where('code', '1-1300')->first(); // Piutang Usaha

        if ($paymentAccount && $receivableAccount) {
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'user_id' => auth()->id() ?? 1,
                'date' => now(),
                'description' => 'Pelunasan Piutang - ' . ($receivable->customer->name ?? 'Customer'),
                'source' => 'receivable_payment',
                'source_id' => $payment->id,
            ]);

            // Dr. Kas/Bank
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $paymentAccount->id,
                'debit' => $data['amount'],
                'credit' => 0,
                'notes' => 'Pelunasan Piutang - ' . ($receivable->customer->name ?? 'Customer'),
            ]);

            // Cr. Piutang Usaha
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $receivableAccount->id,
                'debit' => 0,
                'credit' => $data['amount'],
                'notes' => 'Pengurangan Piutang - ' . ($receivable->customer->name ?? 'Customer'),
            ]);

            // Post journal
            $entry->post();
        }

        \DB::commit();
        return $payment;

    } catch (\Exception $e) {
        \DB::rollBack();
        throw $e;
    }
}    }
