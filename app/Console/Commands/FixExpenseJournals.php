<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Console\Command;

class FixExpenseJournals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:fix-expense-journals {--dry-run : Only report what would change, without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove orphaned/out-of-sync expense journals (from past edits/deletes that did not update the ledger) and repost them from current Expense data, then recalculate account balances';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->info('=== DRY RUN: tidak ada perubahan yang disimpan ===');
        }

        $service = new AccountingService();

        // 1. Orphaned journals: the expense that created them was deleted, but the
        // journal (and its effect on the account balance) was left behind.
        $orphaned = JournalEntry::with('lines')
            ->where('source', 'expense')
            ->whereNotIn('source_id', Expense::pluck('id'))
            ->get();

        foreach ($orphaned as $entry) {
            $this->line("  Jurnal yatim: {$entry->entry_number} (pengeluaran #{$entry->source_id} sudah dihapus)");
            if (!$dryRun) {
                if ($entry->is_posted) {
                    $entry->reverse();
                }
                $entry->lines()->delete();
                $entry->delete();
            }
        }
        $this->info("Jurnal yatim ditemukan: {$orphaned->count()}");

        // 2. Existing expenses whose journal no longer matches the current amount/account
        // (from edits made before the auto-journal was kept in sync), or that never got a
        // journal posted at all despite having an account set.
        $mismatched = 0;
        $expenses = Expense::whereNotNull('account_id')->get();

        foreach ($expenses as $expense) {
            $entries = JournalEntry::with('lines')
                ->where('source', 'expense')
                ->where('source_id', $expense->id)
                ->get();

            $totalDebit = $entries->sum(fn ($entry) => $entry->lines->sum('debit'));
            $inSync = $entries->isNotEmpty() && abs($totalDebit - (float) $expense->amount) < 0.01;

            if (!$inSync) {
                $mismatched++;
                $this->line("  Tidak sinkron: pengeluaran #{$expense->id} \"{$expense->description}\" (Rp " . number_format((float) $expense->amount, 0, ',', '.') . ")");

                if (!$dryRun) {
                    foreach ($entries as $entry) {
                        if ($entry->is_posted) {
                            $entry->reverse();
                        }
                        $entry->lines()->delete();
                        $entry->delete();
                    }

                    try {
                        $service->postExpenseJournal($expense->id, $expense->account_id);
                    } catch (\Exception $e) {
                        $this->error("  Gagal posting ulang pengeluaran #{$expense->id}: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info("Pengeluaran tidak sinkron ditemukan: {$mismatched}");

        if (!$dryRun && ($orphaned->count() > 0 || $mismatched > 0)) {
            $this->info('Menghitung ulang saldo semua akun dari jurnal yang sudah bersih...');
            $service->recalculateAccountBalances();
        }

        $this->info('Selesai.');

        return 0;
    }
}
