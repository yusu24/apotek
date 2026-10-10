<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;

class SyncReturnReceivables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-return-receivables {--dry-run : Simulasi tanpa menyimpan perubahan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi saldo piutang dan jurnal retur penjualan tempo yang belum memotong piutang';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('=== MENJALANKAN MODE SIMULASI (DRY-RUN) - TIDAK ADA PERUBAHAN DATABASE ===');
        }

        $this->info('1. Memeriksa saldo Piutang (Receivable) untuk transaksi yang memiliki Retur...');
        $receivables = Receivable::with(['sale', 'customer'])->get();
        $fixedReceivablesCount = 0;

        foreach ($receivables as $receivable) {
            if (!$receivable->sale_id) continue;

            $totalReturned = (float) SalesReturn::where('sale_id', $receivable->sale_id)->sum('total_amount');
            if ($totalReturned <= 0) continue;

            $sale = $receivable->sale;
            $cashDp = $sale ? (float) $sale->cash_amount : 0;
            $payments = (float) ReceivablePayment::where('receivable_id', $receivable->id)->sum('amount');
            $totalPaid = $cashDp + $payments;

            $initialReceivable = max(0, (float) $receivable->amount - $cashDp);
            $expectedRemaining = max(0, $initialReceivable - $payments - $totalReturned);

            if (abs((float)$receivable->remaining_balance - $expectedRemaining) > 0.01) {
                $oldBalance = number_format($receivable->remaining_balance, 0, ',', '.');
                $newBalance = number_format($expectedRemaining, 0, ',', '.');
                $invNo = $sale->invoice_no ?? "ID #{$receivable->sale_id}";
                $customerName = $receivable->customer->name ?? 'Customer';

                $this->line("  [Koreksi Piutang] Nota: {$invNo} ({$customerName}) | Saldo Lama: Rp {$oldBalance} -> Saldo Baru: Rp {$newBalance}");
                $fixedReceivablesCount++;

                if (!$dryRun) {
                    $receivable->remaining_balance = $expectedRemaining;
                    if ($expectedRemaining <= 0.01) {
                        $receivable->status = 'paid';
                        $receivable->remaining_balance = 0;
                    } else {
                        $receivable->status = 'partial';
                    }
                    $note = " [Koreksi Sinkronisasi Retur: -Rp " . number_format($totalReturned, 0, ',', '.') . "]";
                    $receivable->notes = trim(($receivable->notes ?? '') . $note);
                    $receivable->save();
                }
            }
        }
        $this->info("Total piutang yang diperbaiki: {$fixedReceivablesCount}");

        $this->info('2. Memeriksa jurnal retur penjualan (Sales Return) untuk penjualan tempo...');
        $cashAccount = Account::where('code', '1-1100')->first(); // Kas
        $receivableAccount = Account::where('code', '1-1300')->first(); // Piutang Usaha
        $fixedJournalsCount = 0;

        if ($cashAccount && $receivableAccount) {
            $returns = SalesReturn::with('sale')->get();
            foreach ($returns as $retur) {
                $sale = $retur->sale;
                if (!$sale || $sale->payment_method !== 'tempo') continue;

                $journal = JournalEntry::where('source', 'sales_return')
                    ->where('source_id', $retur->id)
                    ->with('lines')
                    ->first();

                if (!$journal) continue;

                // Cari baris jurnal yang mengkredit Kas (1-1100)
                $cashCreditLine = $journal->lines
                    ->where('account_id', $cashAccount->id)
                    ->where('credit', '>', 0)
                    ->first();

                if ($cashCreditLine) {
                    $this->line("  [Koreksi Jurnal] Retur: {$retur->return_no} (Jurnal: {$journal->entry_number}) dialihkan dari Kas -> Piutang Usaha (Rp " . number_format($cashCreditLine->credit, 0, ',', '.') . ")");
                    $fixedJournalsCount++;

                    if (!$dryRun) {
                        $cashCreditLine->update([
                            'account_id' => $receivableAccount->id,
                            'notes' => 'Pengurangan Piutang - ' . $retur->return_no,
                        ]);
                    }
                }
            }
        }
        $this->info("Total jurnal retur yang diperbaiki: {$fixedJournalsCount}");

        if (!$dryRun && ($fixedReceivablesCount > 0 || $fixedJournalsCount > 0)) {
            $this->info('3. Menghitung ulang saldo akun akuntansi (Recalculate balances)...');
            try {
                $accountingService = new AccountingService();
                $accountingService->recalculateAccountBalances();
                $this->info('Saldo akun buku besar berhasil disinkronkan!');
            } catch (\Exception $e) {
                $this->error('Gagal menghitung ulang saldo akun: ' . $e->getMessage());
            }
        }

        $this->info('Selesai.');
        return 0;
    }
}
