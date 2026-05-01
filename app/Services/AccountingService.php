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
            // Dr. Kas & Dr. Piutang (if tempo)
            $receivableAccount = Account::where('code', '1-1300')->first(); // Piutang Usaha
            
            if ($sale->payment_method === 'tempo' && $receivableAccount) {
                $cashPortion = (float)$sale->cash_amount;
                $receivablePortion = (float)$sale->grand_total - $cashPortion;

                if ($cashPortion > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $cashAccount->id,
                        'debit' => $cashPortion,
                        'credit' => 0,
                        'notes' => 'DP Penjualan ' . $sale->invoice_no,
                    ]);
                }

                if ($receivablePortion > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $receivableAccount->id,
                        'debit' => $receivablePortion,
                        'credit' => 0,
                        'notes' => 'Piutang Penjualan ' . $sale->invoice_no,
                    ]);
                }
            } else {
                // Regular Cash Sale
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $cashAccount->id,
                    'debit' => $sale->grand_total,
                    'credit' => 0,
                    'notes' => 'Penjualan ' . $sale->invoice_no,
                ]);
            }

            // Cr. Penjualan & PPN
            $ppnAccount = Account::where('code', '2-1400')->first(); // PPN Keluaran

            // Determine DPP (Revenue)
            $dpp = (float)$sale->dpp;
            if ($dpp < 0.01) {
                $dpp = (float)$sale->grand_total - (float)$sale->tax - (float)$sale->rounding;
            }

            // Cr. Penjualan (Revenue = DPP)
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $dpp,
                'notes' => 'Penjualan (DPP) ' . $sale->invoice_no,
            ]);

            // Cr. PPN Keluaran (Liability = Tax)
            if ((float)$sale->tax > 0 && $ppnAccount) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $ppnAccount->id,
                    'debit' => 0,
                    'credit' => (float)$sale->tax,
                    'notes' => 'PPN Penjualan ' . $sale->invoice_no,
                ]);
            }
            
            // Cr/Dr. Rounding (Selisih Pembulatan)
            if (abs((float)$sale->rounding) > 0.01) {
                $roundingAccount = Account::where('code', '5-2300')->first(); // Use General Expense as fallback
                if ($roundingAccount) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $roundingAccount->id,
                        'debit' => $sale->rounding < 0 ? abs($sale->rounding) : 0,
                        'credit' => $sale->rounding > 0 ? $sale->rounding : 0,
                        'notes' => 'Pembulatan - ' . $sale->invoice_no,
                    ]);
                }
            }

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
     * Repair/Regenerate missing journal entries for sales
     */
    public function repairMissingJournals(): array
    {
        $sales = Sale::all();
        $repairedCount = 0;
        $errors = [];

        foreach ($sales as $sale) {
            // Check if journal exists
            if (!JournalEntry::where('source', 'sale')->where('source_id', $sale->id)->exists()) {
                try {
                    $this->postSaleJournal($sale->id);
                    $repairedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Sale ID {$sale->id} (INV: {$sale->invoice_no}): " . $e->getMessage();
                }
            }
        }

        return [
            'total' => $sales->count(),
            'repaired' => $repairedCount,
            'errors' => $errors
        ];
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
            $paymentAccount = null;
            if ($goodsReceipt->payment_method === 'transfer' && $goodsReceipt->bank_account_id) {
                $paymentAccount = Account::find($goodsReceipt->bank_account_id);
            }

            if (!$paymentAccount) {
                $paymentAccount = match($goodsReceipt->payment_method) {
                    'cash' => Account::where('code', '1-1100')->first(), // Kas
                    'transfer' => Account::where('code', '1-1200')->first(), // Bank (Fallback)
                    'due_date' => Account::where('code', '2-1200')->first(), // Utang Jatuh Tempo
                    default => Account::where('code', '1-1100')->first(),
                };
            }

            if (!$inventoryAccount || !$paymentAccount) {
                $missing = [];
                if (!$inventoryAccount) $missing[] = "Persediaan (1-1400)";
                if (!$paymentAccount) $missing[] = "Akun Pembayaran (" . ($goodsReceipt->payment_method ?? 'cash') . ")";
                
                throw new \Exception("Akun akuntansi berikut tidak ditemukan: " . implode(', ', $missing) . ". Silakan jalankan 'php artisan db:seed --class=AccountSeeder'.");
            }

            // Calculate total purchase
            $totalPurchase = collect($goodsReceipt->items)->sum(function($item) {
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
            
            $paymentAccount = null;
            if ($payment->payment_method === 'transfer' && $payment->account_id) {
                $paymentAccount = Account::find($payment->account_id);
            }

            if (!$paymentAccount) {
                $paymentAccount = match($payment->payment_method) {
                    'cash' => Account::where('code', '1-1100')->first(), // Kas
                    'transfer' => Account::where('code', '1-1200')->first(), // Bank
                    default => Account::where('code', '1-1100')->first(),
                };
            }

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
        // REMOVED: Do not overwrite GL balance with Stock Module value automatically. 
        // Sync should happen via Adjustment Journals or Manual Recalculation.
        // if ($inventoryAccount) {
        //    $inventoryAccount->update(['balance' => $totalValue]);
        // }
        
        return $totalValue;
    }

    /**
     * Generate Balance Sheet
     */
    public function getBalanceSheet($startDate = null, $endDate = null): array
    {
        $endDate = $endDate ?? now()->endOfMonth()->format('Y-m-d');
        
        // Calculate inventory value for comparison (Optional, strictly for info)
        // $this->calculateInventoryValue();
        
        // Get all accounts
        $accounts = Account::orderBy('code')->get();
        
        // Calculate balances "As Of End Date"
        // We sum all posted journal lines <= EndDate
        $balances = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.is_posted', true)
            ->whereDate('journal_entries.date', '<=', $endDate)
            ->select('journal_entry_lines.account_id', DB::raw('SUM(journal_entry_lines.debit) as total_debit'), DB::raw('SUM(journal_entry_lines.credit) as total_credit'))
            ->groupBy('journal_entry_lines.account_id')
            ->get()
            ->keyBy('account_id');

        // Apply calculated balances to account objects in memory
        foreach ($accounts as $account) {
            $record = $balances->get($account->id);
            $debit = $record ? $record->total_debit : 0;
            $credit = $record ? $record->total_credit : 0;
            
            $increaseOnDebit = in_array($account->type, ['asset', 'expense']);
            
            if ($increaseOnDebit) {
                $account->balance = $debit - $credit;
            } else {
                $account->balance = $credit - $debit;
            }
        }
        
        // Group by type
        $assets = $accounts->where('type', 'asset');
        $liabilities = $accounts->where('type', 'liability');
        $equity = $accounts->where('type', 'equity');
        
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
        
        // Calculate net income for the period (Revenue - Expense) up to EndDate
        // Note: For Balance Sheet "Retained Earnings", we usually need Net Income 
        // from the beginning of time OR from the beginning of the fiscal year depending on context.
        // Here we take 'Net Income (Current Period)' usually implies YTD or similar.
        // However, for A = L + E to hold, 'Equity' must include ALL historical Retained Earnings.
        // So we calculate Net Income for ALL TIME up to End Date to simulate "Retained Earnings".
        
        $revenueIncome = DB::table('journal_entry_lines')
             ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
             ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
             ->where('journal_entries.is_posted', true)
             ->whereDate('journal_entries.date', '<=', $endDate)
             ->where('accounts.type', 'revenue')
             ->sum(DB::raw('journal_entry_lines.credit - journal_entry_lines.debit'));
             
        $expenseIncome = DB::table('journal_entry_lines')
             ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
             ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
             ->where('journal_entries.is_posted', true)
             ->whereDate('journal_entries.date', '<=', $endDate)
             ->where('accounts.type', 'expense')
             ->sum(DB::raw('journal_entry_lines.debit - journal_entry_lines.credit'));
             
        $netIncome = $revenueIncome - $expenseIncome;
        
        // Balance check
        $balanceCheck = abs($totalAssets - ($totalLiabilities + $totalEquity + $netIncome)) < 0.01;
        
        return [
            'current_assets' => $currentAssets,
            'fixed_assets' => $fixedAssets,
            
            // Sub-grouped Assets
            'current_asset_groups' => [
                'cash' => [
                    'label' => 'Kas & Bank',
                    'description' => 'Saldo tunai & bank dari omset usaha',
                    'accounts' => $currentAssets->where('sub_category', 'cash'),
                    'total' => $currentAssets->where('sub_category', 'cash')->sum('balance'),
                ],
                'receivable' => [
                    'label' => 'Piutang Usaha',
                    'description' => 'Tagihan penjualan tempo (Klinik/Instansi)',
                    'accounts' => $currentAssets->where('sub_category', 'receivable'),
                    'total' => $currentAssets->where('sub_category', 'receivable')->sum('balance'),
                ],
                'inventory' => [
                    'label' => 'Persediaan Obat',
                    'description' => 'Nilai aset barang (Stok Real-time)',
                    'accounts' => $currentAssets->where('sub_category', 'inventory'),
                    'total' => $currentAssets->where('sub_category', 'inventory')->sum('balance'),
                ],
                'other' => [
                    'label' => 'Aset Lancar Lainnya',
                    'description' => 'Aset lancar pendukung lainnya',
                    'accounts' => $currentAssets->whereNotIn('sub_category', ['cash', 'receivable', 'inventory']),
                    'total' => $currentAssets->whereNotIn('sub_category', ['cash', 'receivable', 'inventory'])->sum('balance'),
                ],
            ],
            
            'fixed_asset_groups' => [
                'equipment' => [
                    'label' => 'Peralatan & Inventaris',
                    'accounts' => $fixedAssets->where('sub_category', 'equipment'),
                    'total' => $fixedAssets->where('sub_category', 'equipment')->sum('balance'),
                ],
                'vehicle' => [
                    'label' => 'Kendaraan',
                    'accounts' => $fixedAssets->where('sub_category', 'vehicle'),
                    'total' => $fixedAssets->where('sub_category', 'vehicle')->sum('balance'),
                ],
                'land' => [
                    'label' => 'Tanah',
                    'accounts' => $fixedAssets->where('sub_category', 'land'),
                    'total' => $fixedAssets->where('sub_category', 'land')->sum('balance'),
                ],
                'building' => [
                    'label' => 'Bangunan Usaha',
                    'accounts' => $fixedAssets->where('sub_category', 'building'),
                    'total' => $fixedAssets->where('sub_category', 'building')->sum('balance'),
                ],
                'other' => [
                    'label' => 'Aset Tetap Lainnya',
                    'accounts' => $fixedAssets->whereNotIn('sub_category', ['equipment', 'vehicle', 'land', 'building']),
                    'total' => $fixedAssets->whereNotIn('sub_category', ['equipment', 'vehicle', 'land', 'building'])->sum('balance'),
                ],
            ],

            'total_current_assets' => $totalCurrentAssets,
            'total_fixed_assets'   => $totalFixedAssets,
            'total_assets'         => $totalAssets,

            // Sub-grouped Current Liabilities
            'current_liability_groups' => [
                'trade_payable' => [
                    'label'    => 'Hutang Dagang',
                    'accounts' => $currentLiabilities->where('sub_category', 'trade_payable'),
                    'total'    => $currentLiabilities->where('sub_category', 'trade_payable')->sum('balance'),
                ],
                'salary_payable' => [
                    'label'    => 'Hutang Gaji',
                    'accounts' => $currentLiabilities->where('sub_category', 'salary_payable'),
                    'total'    => $currentLiabilities->where('sub_category', 'salary_payable')->sum('balance'),
                ],
                'tax_payable' => [
                    'label'    => 'Hutang Pajak',
                    'accounts' => $currentLiabilities->where('sub_category', 'tax_payable'),
                    'total'    => $currentLiabilities->where('sub_category', 'tax_payable')->sum('balance'),
                ],
                'bank_payable_current' => [
                    'label'    => 'Hutang Bank/Lembaga Keuangan',
                    'accounts' => $currentLiabilities->where('sub_category', 'bank_payable_current'),
                    'total'    => $currentLiabilities->where('sub_category', 'bank_payable_current')->sum('balance'),
                ],
                'non_bank_payable_current' => [
                    'label'    => 'Hutang Non-Lembaga Keuangan',
                    'accounts' => $currentLiabilities->where('sub_category', 'non_bank_payable_current'),
                    'total'    => $currentLiabilities->where('sub_category', 'non_bank_payable_current')->sum('balance'),
                ],
            ],

            // Sub-grouped Long-Term Liabilities
            'long_term_liability_groups' => [
                'bank_payable_longterm' => [
                    'label'    => 'Hutang Bank/Lembaga Keuangan',
                    'accounts' => $longTermLiabilities->where('sub_category', 'bank_payable_longterm'),
                    'total'    => $longTermLiabilities->where('sub_category', 'bank_payable_longterm')->sum('balance'),
                ],
                'non_bank_payable_longterm' => [
                    'label'    => 'Hutang Non-Lembaga Keuangan',
                    'accounts' => $longTermLiabilities->where('sub_category', 'non_bank_payable_longterm'),
                    'total'    => $longTermLiabilities->where('sub_category', 'non_bank_payable_longterm')->sum('balance'),
                ],
            ],

            'current_liabilities'         => $currentLiabilities,
            'long_term_liabilities'       => $longTermLiabilities,
            'total_current_liabilities'   => $totalCurrentLiabilities,
            'total_long_term_liabilities' => $totalLongTermLiabilities,
            'total_liabilities'           => $totalLiabilities,

            // Sub-grouped Equity
            'equity_groups' => [
                'paid_in_capital' => [
                    'label'    => 'Modal Sendiri',
                    'accounts' => $equity->where('sub_category', 'paid_in_capital'),
                    'total'    => $equity->where('sub_category', 'paid_in_capital')->sum('balance'),
                ],
                'retained_earnings' => [
                    'label'    => 'Laba Ditahan',
                    'accounts' => $equity->where('sub_category', 'retained_earnings'),
                    'total'    => $equity->where('sub_category', 'retained_earnings')->sum('balance'),
                ],
            ],

            'equity'       => $equity,
            'total_equity' => $totalEquity,
            'net_income'   => $netIncome,

            'balance_check' => $balanceCheck,
            'start_date'    => $startDate,
            'end_date'      => $endDate,
        ];
    }

    /**
     * Generate Income Statement
     */
    public function getIncomeStatement($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $endDate ?? now()->endOfMonth()->format('Y-m-d');
        
        // 1. Get Balances from Journal Entries within Period
        $periodMovements = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->where('journal_entries.is_posted', true)
            ->whereDate('journal_entries.date', '>=', $startDate)
            ->whereDate('journal_entries.date', '<=', $endDate)
            ->whereIn('accounts.type', ['revenue', 'expense'])
            ->select(
                'accounts.id', 
                'accounts.code', 
                'accounts.name', 
                'accounts.category',
                'accounts.type',
                DB::raw('SUM(journal_entry_lines.debit) as period_debit'),
                DB::raw('SUM(journal_entry_lines.credit) as period_credit')
            )
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.category', 'accounts.type')
            ->get();
            
        // 2. Process Accounts & Calculate Net Amount
        $revenueAccounts = collect();
        $cogsAccounts = collect();
        $operatingExpenseAccounts = collect();
        $taxAccounts = collect();
        $otherExpenseAccounts = collect();
        
        foreach ($periodMovements as $account) {
            $debit = (float) $account->period_debit;
            $credit = (float) $account->period_credit;
            
            // Calculate Net Amount based on Normal Balance
            // Revenue: Credit - Debit (Positive is Good)
            // Expense: Debit - Credit (Positive is Expense)
            $amount = ($account->type === 'revenue') ? ($credit - $debit) : ($debit - $credit);
            
            // Skip if zero balance (optional, but cleaner)
            if (abs($amount) < 0.01) continue;
            
            $accountObj = (object) [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'category' => $account->category,
                'amount' => $amount
            ];

            if ($account->type === 'revenue') {
                $revenueAccounts->push($accountObj);
            } else {
                // Categorize Expenses
                $cat = strtolower($account->category ?? '');
                $name = strtolower($account->name ?? '');
                
                if (str_contains($cat, 'hpp') || str_contains($cat, 'pokok') || str_contains($name, 'hpp')) {
                    $cogsAccounts->push($accountObj);
                } elseif (str_contains($cat, 'pajak') || str_contains($name, 'pajak') || str_contains($cat, 'tax')) {
                    $taxAccounts->push($accountObj);
                } elseif ($cat === 'beban lain-lain' || $cat === 'other') {
                    $otherExpenseAccounts->push($accountObj);
                } else {
                    // Default to Operating
                    $operatingExpenseAccounts->push($accountObj);
                }
            }
        }
        
        // 3. Calculate Totals
        $totalRevenue = $revenueAccounts->sum('amount');
        $totalCOGS = $cogsAccounts->sum('amount');
        
        // Gross Profit (Laba Kotor)
        $grossProfit = $totalRevenue - $totalCOGS;
        
        $totalOperatingExpenses = $operatingExpenseAccounts->sum('amount');
        $totalOtherExpenses = $otherExpenseAccounts->sum('amount');
        $totalTaxExpenses = $taxAccounts->sum('amount');
        
        // Net Income Before Tax (Laba Sebelum Pajak / EBIT)
        $netIncomeBeforeTax = $grossProfit - ($totalOperatingExpenses + $totalOtherExpenses);
        
        // Net Income (Laba Bersih)
        $netIncome = $netIncomeBeforeTax - $totalTaxExpenses;
        
        return [
            'revenue_accounts' => $revenueAccounts,
            'total_revenue' => $totalRevenue,
            
            'cogs_accounts' => $cogsAccounts,
            'total_cogs' => $totalCOGS,
            'gross_profit' => $grossProfit,
            
            'operating_expense_accounts' => $operatingExpenseAccounts,
            'other_expense_accounts' => $otherExpenseAccounts,
            'tax_accounts' => $taxAccounts,
            
            'total_operating_expenses' => $totalOperatingExpenses,
            'total_other_expenses' => $totalOtherExpenses,
            'total_tax_expenses' => $totalTaxExpenses,
            
            'net_income_before_tax' => $netIncomeBeforeTax,
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

        $receipts = GoodsReceipt::with('purchaseOrder.supplier')
            ->whereIn('payment_status', $statuses)
            ->get();
            
        $agingData = [
            '0-7' => [],
            '8-15' => [],
            '16-30' => [],
            '31-45' => [],
            '45+' => [],
            'summary' => [
                '0-7' => 0,
                '8-15' => 0,
                '16-30' => 0,
                '31-45' => 0,
                '45+' => 0,
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
            
            if ($age <= 7) {
                $agingData['0-7'][] = $item;
                $agingData['summary']['0-7'] += $outstanding;
            } elseif ($age <= 15) {
                $agingData['8-15'][] = $item;
                $agingData['summary']['8-15'] += $outstanding;
            } elseif ($age <= 30) {
                $agingData['16-30'][] = $item;
                $agingData['summary']['16-30'] += $outstanding;
            } elseif ($age <= 45) {
                $agingData['31-45'][] = $item;
                $agingData['summary']['31-45'] += $outstanding;
            } else {
                $agingData['45+'][] = $item;
                $agingData['summary']['45+'] += $outstanding;
            }
            
            $agingData['summary']['total'] += $outstanding;
        }
        
        // Sort each bucket by age descending (oldest first)
        foreach (['0-7', '8-15', '16-30', '31-45', '45+'] as $key) {
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
        '0-7' => [],
        '8-15' => [],
        '16-30' => [],
        '31-45' => [],
        '45+' => [],
        'summary' => [
            '0-7' => 0,
            '8-15' => 0,
            '16-30' => 0,
            '31-45' => 0,
            '45+' => 0,
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
        
        if ($age <= 7) {
            $agingData['0-7'][] = $item;
            $agingData['summary']['0-7'] += $outstanding;
        } elseif ($age <= 15) {
            $agingData['8-15'][] = $item;
            $agingData['summary']['8-15'] += $outstanding;
        } elseif ($age <= 30) {
            $agingData['16-30'][] = $item;
            $agingData['summary']['16-30'] += $outstanding;
        } elseif ($age <= 45) {
            $agingData['31-45'][] = $item;
            $agingData['summary']['31-45'] += $outstanding;
        } else {
            $agingData['45+'][] = $item;
            $agingData['summary']['45+'] += $outstanding;
        }
        
        $agingData['summary']['total'] += $outstanding;
    }
    
    // Sort each bucket by age descending (oldest first)
    foreach (['0-7', '8-15', '16-30', '31-45', '45+'] as $key) {
        usort($agingData[$key], function($a, $b) {
            return $b['age'] <=> $a['age'];
        });
    }
    
    return $agingData;
}

/**
 * Get Grouped Aging Report for PDF
 */
public function getGroupedAgingReport($type = 'ar', $includePaid = false)
{
    $buckets = [
        '7' => ['label' => '<=7', 'max' => 7],
        '15' => ['label' => '<=15', 'max' => 15],
        '30' => ['label' => '<=30', 'max' => 30],
        '45' => ['label' => '<=45', 'max' => 45],
        'plus' => ['label' => '>45', 'max' => 999999],
    ];

    $groupedData = [];
    $totalSummary = array_fill_keys(array_keys($buckets), 0);
    $totalSummary['total'] = 0;

    if ($type === 'ar') {
        $statuses = ['partial', 'unpaid'];
        if ($includePaid) $statuses[] = 'paid';
        
        $items = \App\Models\Receivable::with(['customer', 'sale'])
            ->whereIn('status', $statuses)
            ->get();
            
        foreach ($items as $item) {
            $age = $item->created_at->diffInDays(now());
            $outstanding = $item->remaining_balance;
            if (!$includePaid && $outstanding <= 0.01) continue;

            $entityId = $item->customer_id ?? 0;
            $entityName = $item->customer->name ?? 'Unknown';
            
            if (!isset($groupedData[$entityId])) {
                $groupedData[$entityId] = [
                    'entity_id' => $entityId,
                    'code' => 'C-' . str_pad($entityId, 5, '0', STR_PAD_LEFT),
                    'name' => $entityName,
                    'limit' => -1,
                    'total' => 0,
                    'buckets' => array_fill_keys(array_keys($buckets), 0),
                    'invoices' => []
                ];
            }

            $bucketKey = $this->getBucketKeyForGrouped($age);
            $groupedData[$entityId]['buckets'][$bucketKey] += $outstanding;
            
            $groupedData[$entityId]['invoices'][] = [
                'number' => $item->sale->invoice_no ?? '-',
                'date' => $item->created_at->format('d/m/Y'),
                'dueDate' => $item->due_date ? $item->due_date->format('d/m/Y') : '-',
                'age' => $age,
                'amount' => $outstanding,
                'bucket' => $bucketKey
            ];
        }
    } else {
        $statuses = ['pending', 'partial'];
        if ($includePaid) $statuses[] = 'paid';
        
        $items = GoodsReceipt::with('purchaseOrder.supplier')
            ->whereIn('payment_status', $statuses)
            ->get();
            
        foreach ($items as $item) {
            $age = \Carbon\Carbon::parse($item->received_date)->diffInDays(now());
            $outstanding = $item->total_amount - $item->paid_amount;
            if (!$includePaid && $outstanding <= 0.01) continue;

            $entityId = $item->purchaseOrder->supplier_id ?? 0;
            $entityName = $item->purchaseOrder->supplier->name ?? 'Unknown';
            
            if (!isset($groupedData[$entityId])) {
                $groupedData[$entityId] = [
                    'entity_id' => $entityId,
                    'code' => 'S-' . str_pad($entityId, 5, '0', STR_PAD_LEFT),
                    'name' => $entityName,
                    'limit' => -1,
                    'total' => 0,
                    'buckets' => array_fill_keys(array_keys($buckets), 0),
                    'invoices' => []
                ];
            }

            $bucketKey = $this->getBucketKeyForGrouped($age);
            $groupedData[$entityId]['buckets'][$bucketKey] += $outstanding;
            
            $groupedData[$entityId]['invoices'][] = [
                'number' => $item->delivery_note_number,
                'date' => \Carbon\Carbon::parse($item->received_date)->format('d/m/Y'),
                'dueDate' => $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') : '-',
                'age' => $age,
                'amount' => $outstanding,
                'bucket' => $bucketKey
            ];
        }
    }

    // Calculate entity totals and global summary
    foreach ($groupedData as &$entity) {
        $entity['total'] = array_sum($entity['buckets']);
        foreach ($entity['buckets'] as $k => $v) {
            $totalSummary[$k] += $v;
        }
        $totalSummary['total'] += $entity['total'];
    }

    return [
        'buckets' => $buckets,
        'groupedData' => $groupedData,
        'totalSummary' => $totalSummary,
        'type' => $type
    ];
}

private function getBucketKeyForGrouped($age)
{
    if ($age <= 7) return '7';
    if ($age <= 15) return '15';
    if ($age <= 30) return '30';
    if ($age <= 45) return '45';
    return 'plus';
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

    DB::beginTransaction();
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
        $paymentAccount = null;
        if (($data['payment_method'] ?? 'cash') === 'transfer' && !empty($data['account_id'])) {
            $paymentAccount = Account::find($data['account_id']);
        }

        if (!$paymentAccount) {
            $paymentAccount = match($data['payment_method'] ?? 'cash') {
                'cash' => Account::where('code', '1-1100')->first(), // Kas
                'transfer' => Account::where('code', '1-1200')->first(), // Bank (Fallback)
                default => Account::where('code', '1-1100')->first(),
            };
        }
        
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

        DB::commit();
        return $payment;

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
    }

    /**
     * Process Supplier Payment (Hutang)
     */
    public function processSupplierPayment($goodsReceiptId, $data)
    {
        $gr = GoodsReceipt::findOrFail($goodsReceiptId);
        $outstanding = $gr->total_amount - $gr->paid_amount;

        if ($data['amount'] > $outstanding + 0.01) {
            throw new \Exception('Jumlah pembayaran melebihi sisa hutang.');
        }

        DB::beginTransaction();
        try {
            // 1. Create Payment Record
            $payment = \App\Models\SupplierPayment::create([
                'goods_receipt_id' => $gr->id,
                'user_id' => auth()->id() ?? 1,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'] ?? 'cash',
                'account_id' => $data['account_id'] ?? null,
                'payment_date' => $data['date'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. Update GR Balance and Status
            $gr->updatePaymentStatus();

            // 3. Create Journal Entry (Accounts Payable Debit, Cash/Bank Credit)
            $paymentAccount = null;
            if (($data['payment_method'] ?? 'cash') === 'transfer' && !empty($data['account_id'])) {
                $paymentAccount = Account::find($data['account_id']);
            }

            if (!$paymentAccount) {
                $paymentAccount = match($data['payment_method'] ?? 'cash') {
                    'cash' => Account::where('code', '1-1100')->first(), // Kas
                    'transfer' => Account::where('code', '1-1200')->first(), // Bank (Fallback)
                    default => Account::where('code', '1-1100')->first(),
                };
            }
            
            $payableAccount = Account::where('code', '2-1200')->first(); // Utang Jatuh Tempo

            if ($paymentAccount && $payableAccount) {
                $entry = JournalEntry::create([
                    'entry_number' => JournalEntry::generateEntryNumber(),
                    'user_id' => auth()->id() ?? 1,
                    'date' => $data['date'] ?? now(),
                    'description' => 'Pelunasan Hutang - ' . ($gr->purchaseOrder->supplier->name ?? 'Supplier'),
                    'source' => 'supplier_payment',
                    'source_id' => $payment->id,
                ]);

                // Dr. Utang Jatuh Tempo
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $payableAccount->id,
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'notes' => 'Pelunasan Hutang - ' . ($gr->purchaseOrder->supplier->name ?? 'Supplier'),
                ]);

                // Cr. Kas/Bank
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $paymentAccount->id,
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'notes' => 'Pembayaran Hutang - ' . ($gr->purchaseOrder->supplier->name ?? 'Supplier'),
                ]);

                // Post journal
                $entry->post();
            }

            DB::commit();
            return $payment;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate Cash Flow Statement (Direct Method approximation)
     */
    public function getCashFlowStatement($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $endDate ?? now()->endOfMonth()->format('Y-m-d');

        // Get Cash and Bank accounts IDs
        $cashBankAccountIds = Account::whereIn('category', ['cash', 'bank'])
             ->orWhere('code', 'LIKE', '1-1%') // Fallback assumption for assets
             ->pluck('id');

        // 1. Operating Activities
        
        // Receipts from customers (Sales - Returns)
        $salesReceipts = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.account_id', $cashBankAccountIds)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->where('journal_entries.source', 'sale')
            ->sum('journal_entry_lines.debit');
            
        // Include Receivable Payments as Receipts
        $receivableReceipts = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.account_id', $cashBankAccountIds)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->where('journal_entries.source', 'receivable_payment')
            ->sum('journal_entry_lines.debit');
            
        $salesRefunds = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.account_id', $cashBankAccountIds)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->where('journal_entries.source', 'sales_return')
            ->sum('journal_entry_lines.credit');

        $receiptsFromCustomers = ($salesReceipts + $receivableReceipts) - $salesRefunds;

        // Payment to suppliers (Purchases + Supplier Payments - Returns)
        $supplierPayments = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.account_id', $cashBankAccountIds)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->whereIn('journal_entries.source', ['purchase', 'supplier_payment'])
            ->sum('journal_entry_lines.credit');
            
        $purchaseRefunds = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.account_id', $cashBankAccountIds)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->where('journal_entries.source', 'purchase_return')
            ->sum('journal_entry_lines.debit');
            
        $paymentsToSuppliers = -($supplierPayments - $purchaseRefunds); // Outflow is negative

        // Operating Expenses
        $operatingExpenses = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.account_id', $cashBankAccountIds)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->where('journal_entries.source', 'expense')
            ->sum('journal_entry_lines.credit');
            
        $paymentsForExpenses = -($operatingExpenses); // Outflow is negative

        // Other Operating (Manual entries affecting cash not caught above? For now 0)
        $otherOperating = 0;

        $netCashOperating = $receiptsFromCustomers + $paymentsToSuppliers + $paymentsForExpenses + $otherOperating;

        // 2. Investing Activities
        // Placeholder for asset purchases/sales logic
        $purchaseAssets = 0;
        $saleAssets = 0;
        $otherInvesting = 0;
        
        $netCashInvesting = $purchaseAssets + $saleAssets + $otherInvesting;

        // 3. Financing Activities
        // Placeholder for loans/equity
        $loans = 0;
        $equity = 0; // Capital injection
        
        $netCashFinancing = $loans + $equity;

        // Totals
        $netIncrease = $netCashOperating + $netCashInvesting + $netCashFinancing;
        
        // Beginning Balance (Sum of all cash transactions before start date)
        $beginningBalance = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.account_id', $cashBankAccountIds)
            ->where('journal_entries.is_posted', true)
            ->where('journal_entries.date', '<', $startDate)
            ->select(DB::raw('SUM(debit) - SUM(credit) as balance'))
            ->value('balance') ?? 0;

        $endingBalance = $beginningBalance + $netIncrease;

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            
            // Operating
            'receipts_from_customers' => $receiptsFromCustomers,
            'payments_to_suppliers' => $paymentsToSuppliers,
            'payments_for_expenses' => $paymentsForExpenses,
            'other_operating' => $otherOperating,
            'net_cash_operating' => $netCashOperating,
            
            // Investing
            'purchase_assets' => $purchaseAssets,
            'sale_assets' => $saleAssets,
            'other_investing' => $otherInvesting,
            'net_cash_investing' => $netCashInvesting,
            
            // Financing
            'loans' => $loans,
            'equity' => $equity,
            'net_cash_financing' => $netCashFinancing,
            
            // Summary
            'net_increase' => $netIncrease,
            'beginning_balance' => $beginningBalance,
            'ending_balance' => $endingBalance,
        ];
    }

    /**
     * Get General Ledger Report Data
     * Supports single account or all accounts
     */
    public function getLedgerReport(string $startDate, string $endDate, ?int $accountId = null, ?string $search = null): array
    {
        $accounts = Account::active();
        
        if ($accountId) {
            $accounts->where('id', $accountId);
        }
        
        $accounts = $accounts->orderBy('code')->get();
        $ledgerData = [];

        foreach ($accounts as $account) {
            $increaseOnDebit = in_array($account->type, ['asset', 'expense']);
            
            // 1. Calculate Opening Balance
            $preLines = JournalEntryLine::whereHas('journalEntry', function($q) use ($startDate) {
                    $q->whereDate('date', '<', $startDate)->where('is_posted', true);
                })
                ->where('account_id', $account->id)
                ->get();

            $openingBalance = 0;
            foreach ($preLines as $line) {
                if ($increaseOnDebit) {
                    $openingBalance += ($line->debit - $line->credit);
                } else {
                    $openingBalance += ($line->credit - $line->debit);
                }
            }

            // 2. Get Transaction Lines
            $linesQuery = JournalEntryLine::with('journalEntry')
                ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                    $q->whereDate('date', '>=', $startDate)
                      ->whereDate('date', '<=', $endDate)
                      ->where('is_posted', true);
                })
                ->where('account_id', $account->id)
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id');

            if ($search) {
                $linesQuery->where(function($q) use ($search) {
                    $q->where('journal_entries.description', 'like', "%{$search}%")
                      ->orWhere('journal_entries.entry_number', 'like', "%{$search}%")
                      ->orWhere('journal_entry_lines.notes', 'like', "%{$search}%");
                });
            }

            $lines = $linesQuery->orderBy('journal_entries.date')
                ->orderBy('journal_entries.id')
                ->select('journal_entry_lines.*')
                ->get();

            // 3. Calculate Running Balance
            $runningBalance = $openingBalance;
            foreach ($lines as $line) {
                if ($increaseOnDebit) {
                    $runningBalance += ($line->debit - $line->credit);
                } else {
                    $runningBalance += ($line->credit - $line->debit);
                }
                $line->running_balance = $runningBalance;
            }

            $ledgerData[] = [
                'account' => $account,
                'opening_balance' => $openingBalance,
                'lines' => $lines,
                'ending_balance' => $runningBalance,
            ];
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $ledgerData,
        ];
    }

    /**
     * Post asset depreciation journal automatically
     */
    public function postAssetDepreciation(int $assetId, string $periodDate): ?\App\Models\AssetDepreciation
    {
        $asset = \App\Models\FixedAsset::findOrFail($assetId);
        $date = \Carbon\Carbon::parse($periodDate)->endOfMonth();
        $dateKey = $date->format('Y-m-d');

        // Check if already depreciated for this month
        if (\App\Models\AssetDepreciation::where('fixed_asset_id', $assetId)->where('period_date', $dateKey)->exists()) {
            return null;
        }

        $depreciationAmount = $this->calculateDepreciationAmount($asset, $date->month, $date->year);

        if ($depreciationAmount <= 0) {
            return null;
        }

        DB::beginTransaction();
        try {
            // Create journal entry
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'date' => $dateKey,
                'description' => 'Penyusutan Aset - ' . $asset->asset_name . ' (' . $date->format('M Y') . ')',
                'source' => 'asset_depreciation',
                'source_id' => $assetId,
                'user_id' => Auth::id() ?? 1,
            ]);

            // Dr. Beban Penyusutan
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $asset->depreciation_expense_account_id,
                'debit' => $depreciationAmount,
                'credit' => 0,
                'notes' => 'Penyusutan ' . $asset->asset_name,
            ]);

            // Cr. Akumulasi Penyusutan
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $asset->accumulated_depreciation_account_id,
                'debit' => 0,
                'credit' => $depreciationAmount,
                'notes' => 'Akumulasi Penyusutan ' . $asset->asset_name,
            ]);

            // Post journal
            $entry->post();

            // Record Depreciation
            $depreciation = \App\Models\AssetDepreciation::create([
                'fixed_asset_id' => $assetId,
                'journal_entry_id' => $entry->id,
                'period_date' => $dateKey,
                'amount' => $depreciationAmount,
                'book_value_after' => $asset->book_value - $depreciationAmount,
            ]);

            DB::commit();
            return $depreciation;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculate depreciation amount based on ID Tax UU PPh
     */
    public function calculateDepreciationAmount(\App\Models\FixedAsset $asset, int $month, int $year): float
    {
        $taxGroups = \App\Models\FixedAsset::getTaxGroups();
        $group = $taxGroups[$asset->tax_group] ?? null;

        if (!$group) return 0;

        $targetDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
        
        // Don't depreciate if before acquisition
        if ($targetDate->lt(\Carbon\Carbon::parse($asset->acquisition_date)->startOfMonth())) {
            return 0;
        }

        // Don't depreciate if fully depreciated
        if ($asset->book_value <= $asset->salvage_value) {
            return 0;
        }

        $annualRate = ($asset->method === 'declining_balance') ? $group['db_rate'] : $group['sl_rate'];
        $monthlyAmount = 0;

        if ($asset->method === 'straight_line') {
            // SL: (Acquisition Cost - Salvage) * Rate / 12
            $monthlyAmount = ($asset->acquisition_cost - $asset->salvage_value) * $annualRate / 12;
        } else {
            // DB: Book Value * Rate / 12 (Simplified for monthly context in UU PPh)
            // Note: In some practices, DB is annual. Here we use current book value for monthly.
            $monthlyAmount = $asset->book_value * $annualRate / 12;
        }

        // Adjustment for the last month to hit salvage value exactly
        if ($asset->book_value - $monthlyAmount < $asset->salvage_value) {
            $monthlyAmount = $asset->book_value - $asset->salvage_value;
        }

        return (float) round($monthlyAmount, 2);
    }

    /**
     * Recalculate all account balances from scratch based on Journal Entries.
     * Useful for fixing sync issues.
     */
    public function recalculateAccountBalances()
    {
        DB::beginTransaction();
        try {
            // 1. Reset all balances to 0
            Account::query()->update(['balance' => 0]);
            
            // 2. Calculate totals from Posted Journals
            $balances = DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entries.is_posted', true)
                ->select('journal_entry_lines.account_id', DB::raw('SUM(journal_entry_lines.debit) as total_debit'), DB::raw('SUM(journal_entry_lines.credit) as total_credit'))
                ->groupBy('journal_entry_lines.account_id')
                ->get();
                
            // 3. Update Accounts
            foreach ($balances as $balance) {
                $account = Account::find($balance->account_id);
                if ($account) {
                    $increaseOnDebit = in_array($account->type, ['asset', 'expense']);
                    
                    if ($increaseOnDebit) {
                        $newBalance = $balance->total_debit - $balance->total_credit;
                    } else {
                        $newBalance = $balance->total_credit - $balance->total_debit;
                    }
                    
                    $account->update(['balance' => $newBalance]);
                }
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

