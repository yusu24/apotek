<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use Illuminate\Support\Facades\Storage;

class CleanPaymentProofs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-payment-proofs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus file bukti pembayaran QRIS yang berusia lebih dari 2 minggu (14 hari)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembersihan bukti pembayaran QRIS...');

        // Mencari transaksi QRIS dengan bukti pembayaran yang usianya lebih dari 14 hari
        $sales = Sale::whereNotNull('payment_proof')
            ->where('payment_method', 'qris')
            ->where('date', '<', now()->subDays(14))
            ->get();

        $count = 0;
        foreach ($sales as $sale) {
            if ($sale->payment_proof) {
                if (Storage::disk('public')->exists($sale->payment_proof)) {
                    Storage::disk('public')->delete($sale->payment_proof);
                    $this->info("Menghapus berkas bukti untuk invois: {$sale->invoice_no}");
                }
                // Hapus path dari database agar tidak merujuk ke berkas kosong
                $sale->update(['payment_proof' => null]);
                $count++;
            }
        }

        $this->info("Selesai. Berhasil membersihkan {$count} bukti pembayaran QRIS.");
        return Command::SUCCESS;
    }
}
