<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Receivable;
use App\Models\Batch;
use App\Models\Product;
use App\Models\User;
use App\Notifications\UrgentAlertNotification;
use Carbon\Carbon;

class CheckUrgentAlerts extends Command
{
    protected $signature = 'app:check-urgent-alerts';
    protected $description = 'Check for nearing receivables and product expiry';

    public function handle()
    {
        $this->info('Checking for urgent items...');

        // 1. Receivables (Piutang)
        $piutangs = Receivable::where('remaining_balance', '>', 0)
            ->whereDate('due_date', '<=', Carbon::now()->addDays(7))
            ->get();

        if ($piutangs->count() > 0) {
            $this->notifyAdmins('piutang', "Terdapat " . $piutangs->count() . " piutang yang jatuh tempo dalam 7 hari.");
        }

        // 2. Expiry - Already Expired
        $expiredBatches = Batch::with('product')
            ->where('stock_current', '>', 0)
            ->whereDate('expired_date', '<', Carbon::now())
            ->get();

        if ($expiredBatches->count() > 0) {
            $productNames = $expiredBatches->pluck('product.name')->unique()->take(3)->implode(', ');
            $suffix = $expiredBatches->pluck('product.name')->unique()->count() > 3 ? " dan lainnya" : "";
            $this->notifyAdmins('expiry', "PERHATIAN: Produk ($productNames$suffix) SUDAH KADALUWARSA!");
        }

        // 3. Expiry - Nearing (60 days)
        $nearingExpiryBatches = Batch::with('product')
            ->where('stock_current', '>', 0)
            ->whereDate('expired_date', '>=', Carbon::now())
            ->whereDate('expired_date', '<=', Carbon::now()->addDays(60))
            ->get();

        if ($nearingExpiryBatches->count() > 0) {
            $productNames = $nearingExpiryBatches->pluck('product.name')->unique()->take(3)->implode(', ');
            $suffix = $nearingExpiryBatches->pluck('product.name')->unique()->count() > 3 ? " dan lainnya" : "";
            $this->notifyAdmins('expiry', "Mendekati Kadaluwarsa: $productNames$suffix (dalam 60 hari).");
        }
        
        // 4. Out of Stock
        $outOfStockProducts = Product::outOfStock()->get();
        if ($outOfStockProducts->count() > 0) {
            $productNames = $outOfStockProducts->pluck('name')->take(3)->implode(', ');
            $suffix = $outOfStockProducts->count() > 3 ? " dan lainnya" : "";
            $this->notifyAdmins('out_of_stock', "Stok Habis (0): $productNames$suffix.");
        }

        // 5. Low Stock
        $lowStockProducts = Product::lowStock()->get();
        if ($lowStockProducts->count() > 0) {
            $productNames = $lowStockProducts->pluck('name')->take(3)->implode(', ');
            $suffix = $lowStockProducts->count() > 3 ? " dan lainnya" : "";
            $this->notifyAdmins('low_stock', "Stok Menipis (< min): $productNames$suffix.");
        }

        $this->info('Check completed.');
    }

    private function notifyAdmins($type, $message)
    {
        // Get users with permission to manage users or settings (Admins)
        $admins = User::whereHas('permissions', function($q) {
            $q->whereIn('name', ['manage settings', 'manage users']);
        })->orWhereHas('roles', function($q) {
            $q->where('name', 'super-admin');
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new UrgentAlertNotification($type, $message));
        }
    }
}
