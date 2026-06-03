<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Models\GoodsReceipt;
use App\Models\SalesReturn;
use App\Models\PurchaseReturn;
use App\Models\SupplierPayment;
use App\Models\ReceivablePayment;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepairJournals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:repair-journals {--dry-run : Only simulate the repair process without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate missing journal entries for all historical transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('=== RUNNING IN DRY-RUN MODE (NO CHANGES WILL BE SAVED) ===');
        }

        $this->info('Starting journal repair process...');
        $service = new AccountingService();

        // 1. Sales
        $this->info('Processing Sales...');
        $sales = Sale::all();
        $salesCount = 0;
        foreach ($sales as $sale) {
            $exists = JournalEntry::where('source', 'sale')->where('source_id', $sale->id)->exists();
            if (!$exists) {
                $salesCount++;
                if (!$dryRun) {
                    try {
                        $service->postSaleJournal($sale->id);
                    } catch (\Exception $e) {
                        $this->error("Error posting sale {$sale->invoice_no}: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Found " . ($dryRun ? 'missing: ' : 'repaired: ') . "{$salesCount} sales journals.");

        // 2. Goods Receipts (Purchases)
        $this->info('Processing Goods Receipts...');
        $receipts = GoodsReceipt::all();
        $receiptsCount = 0;
        foreach ($receipts as $gr) {
            $exists = JournalEntry::where('source', 'purchase')->where('source_id', $gr->id)->exists();
            if (!$exists) {
                $receiptsCount++;
                if (!$dryRun) {
                    try {
                        $service->postPurchaseJournal($gr->id);
                    } catch (\Exception $e) {
                        $this->error("Error posting Goods Receipt GR-{$gr->id}: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Found " . ($dryRun ? 'missing: ' : 'repaired: ') . "{$receiptsCount} goods receipt journals.");

        // 3. Sales Returns
        $this->info('Processing Sales Returns...');
        $returns = SalesReturn::all();
        $returnsCount = 0;
        foreach ($returns as $sr) {
            $exists = JournalEntry::where('source', 'sales_return')->where('source_id', $sr->id)->exists();
            if (!$exists) {
                $returnsCount++;
                if (!$dryRun) {
                    try {
                        $service->postSalesReturnJournal($sr->id);
                    } catch (\Exception $e) {
                        $this->error("Error posting Sales Return SR-{$sr->id}: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Found " . ($dryRun ? 'missing: ' : 'repaired: ') . "{$returnsCount} sales return journals.");

        // 4. Purchase Returns
        $this->info('Processing Purchase Returns...');
        $pReturns = PurchaseReturn::all();
        $pReturnsCount = 0;
        foreach ($pReturns as $pr) {
            $exists = JournalEntry::where('source', 'purchase_return')->where('source_id', $pr->id)->exists();
            if (!$exists) {
                $pReturnsCount++;
                if (!$dryRun) {
                    try {
                        $service->postPurchaseReturnJournal($pr->id);
                    } catch (\Exception $e) {
                        $this->error("Error posting Purchase Return PR-{$pr->id}: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Found " . ($dryRun ? 'missing: ' : 'repaired: ') . "{$pReturnsCount} purchase return journals.");

        // 5. Supplier Payments
        $this->info('Processing Supplier Payments...');
        $sPayments = SupplierPayment::all();
        $sPaymentsCount = 0;
        foreach ($sPayments as $sp) {
            $exists = JournalEntry::where('source', 'supplier_payment')->where('source_id', $sp->id)->exists();
            if (!$exists) {
                $sPaymentsCount++;
                if (!$dryRun) {
                    try {
                        $service->postSupplierPaymentJournal($sp->id);
                    } catch (\Exception $e) {
                        $this->error("Error posting Supplier Payment ID {$sp->id}: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Found " . ($dryRun ? 'missing: ' : 'repaired: ') . "{$sPaymentsCount} supplier payment journals.");

        // 6. Receivable Payments
        $this->info('Processing Receivable Payments...');
        $rPayments = ReceivablePayment::with('receivable.customer')->get();
        $rPaymentsCount = 0;
        
        $receivableAccount = Account::where('code', '1-1300')->first(); // Piutang Usaha
        
        foreach ($rPayments as $rp) {
            $exists = JournalEntry::where('source', 'receivable_payment')->where('source_id', $rp->id)->exists();
            if (!$exists) {
                $rPaymentsCount++;
                if (!$dryRun) {
                    try {
                        $paymentAccount = null;
                        if (($rp->payment_method ?? 'cash') === 'transfer' && !empty($rp->account_id)) {
                            $paymentAccount = Account::find($rp->account_id);
                        }

                        if (!$paymentAccount) {
                            $paymentAccount = match($rp->payment_method ?? 'cash') {
                                'cash' => Account::where('code', '1-1100')->first(), // Kas
                                'transfer' => Account::where('code', '1-1200')->first(), // Bank (Fallback)
                                default => Account::where('code', '1-1100')->first(),
                            };
                        }

                        if ($paymentAccount && $receivableAccount) {
                            DB::transaction(function () use ($rp, $paymentAccount, $receivableAccount) {
                                $entry = JournalEntry::create([
                                    'entry_number' => JournalEntry::generateEntryNumber(),
                                    'user_id' => $rp->user_id ?? 1,
                                    'date' => $rp->paid_at ?? $rp->created_at ?? now(),
                                    'description' => 'Pelunasan Piutang - ' . ($rp->receivable->customer->name ?? 'Customer'),
                                    'source' => 'receivable_payment',
                                    'source_id' => $rp->id,
                                ]);

                                // Dr. Kas/Bank
                                JournalEntryLine::create([
                                    'journal_entry_id' => $entry->id,
                                    'account_id' => $paymentAccount->id,
                                    'debit' => $rp->amount,
                                    'credit' => 0,
                                    'notes' => 'Pelunasan Piutang - ' . ($rp->receivable->customer->name ?? 'Customer'),
                                ]);

                                // Cr. Piutang Usaha
                                JournalEntryLine::create([
                                    'journal_entry_id' => $entry->id,
                                    'account_id' => $receivableAccount->id,
                                    'debit' => 0,
                                    'credit' => $rp->amount,
                                    'notes' => 'Pengurangan Piutang - ' . ($rp->receivable->customer->name ?? 'Customer'),
                                ]);

                                $entry->post();
                            });
                        } else {
                            $this->error("Error posting Receivable Payment ID {$rp->id}: Missing accounts (payment or receivable account).");
                        }
                    } catch (\Exception $e) {
                        $this->error("Error posting Receivable Payment ID {$rp->id}: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Found " . ($dryRun ? 'missing: ' : 'repaired: ') . "{$rPaymentsCount} receivable payment journals.");

        // 7. Expenses
        $this->info('Processing Expenses...');
        $expenses = Expense::all();
        $expensesCount = 0;
        foreach ($expenses as $expense) {
            $exists = JournalEntry::where('source', 'expense')->where('source_id', $expense->id)->exists();
            if (!$exists && !empty($expense->account_id)) {
                $expensesCount++;
                if (!$dryRun) {
                    try {
                        $service->postExpenseJournal($expense->id, $expense->account_id);
                    } catch (\Exception $e) {
                        $this->error("Error posting Expense ID {$expense->id} ({$expense->description}): " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Found " . ($dryRun ? 'missing: ' : 'repaired: ') . "{$expensesCount} expense journals.");

        // Recalculate balances after saving if not dry-run
        if (!$dryRun && ($salesCount > 0 || $receiptsCount > 0 || $returnsCount > 0 || $pReturnsCount > 0 || $sPaymentsCount > 0 || $rPaymentsCount > 0 || $expensesCount > 0)) {
            $this->info('Recalculating account balances...');
            try {
                $service->recalculateAccountBalances();
                $this->info('Balances recalculated successfully!');
            } catch (\Exception $e) {
                $this->error('Failed to recalculate balances: ' . $e->getMessage());
            }
        }

        $this->info('Repair process completed!');
        return 0;
    }
}
