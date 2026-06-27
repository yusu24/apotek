<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OpeningBalance;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class SyncOpeningBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-opening-balance {--force : Force sync even if opening balance is locked}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hitung nilai persediaan obat dari database dan sinkronisasikan ke Saldo Awal (Ledger 1-1400)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai sinkronisasi Saldo Awal Persediaan...');
        $force = $this->option('force');

        $ob = OpeningBalance::first();
        if ($ob && $ob->locked_at !== null && !$force) {
            $this->error('Error: Neraca Saldo Awal sudah dikunci di aplikasi. Gunakan opsi --force untuk memaksa sinkronisasi.');
            return 1;
        }

        DB::beginTransaction();
        try {
            // 1. Hitung total nilai persediaan aktif
            $totalValue = Batch::where('stock_current', '>', 0)
                ->get()
                ->sum(function ($batch) {
                    return $batch->stock_current * $batch->buy_price;
                });

            $this->info("Nilai Persediaan Terkalkulasi: Rp " . number_format($totalValue, 2));

            if (!$ob) {
                $ob = new OpeningBalance();
                $ob->cash_amount = 0;
                $ob->bank_amount = 0;
                $ob->balance_date = now()->subDay();
                $ob->is_confirmed = true;
                $this->info("Membuat neraca saldo awal baru...");
            }

            $ob->inventory_amount = $totalValue;

            // Seimbangkan Modal Awal: Aset = Liabilitas + Ekuitas
            $totalAssets = (float)$ob->cash_amount + (float)$ob->bank_amount + (float)$ob->inventory_amount;
            foreach ($ob->assets as $asset) {
                $totalAssets += (float)$asset->amount;
            }

            $totalLiabilities = 0;
            foreach ($ob->debts as $debt) {
                $totalLiabilities += (float)$debt->amount;
            }

            $ob->capital_amount = $totalAssets - $totalLiabilities;
            $ob->save();

            // 2. Sinkronkan dengan Jurnal Pembukaan
            $ob->syncJournal();

            DB::commit();
            $this->info("Sukses: Saldo awal disinkronkan. Akun 1-1400 telah diperbarui di Buku Besar!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Gagal sinkronisasi: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
