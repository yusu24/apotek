<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Receivable;
use App\Models\Batch;
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
        $piutangCount = Receivable::where('remaining_balance', '>', 0)
            ->whereDate('due_date', '<=', Carbon::now()->addDays(7))
            ->count();

        if ($piutangCount > 0) {
            $this->notifyAdmins('piutang', "Terdapat $piutangCount piutang yang jatuh tempo dalam 7 hari.");
        }

        // 2. Expiry
        $expiryCount = Batch::where('stock_current', '>', 0)
            ->whereDate('expired_date', '<=', Carbon::now()->addDays(60))
            ->count();

        if ($expiryCount > 0) {
            $this->notifyAdmins('expiry', "Terdapat $expiryCount batch produk yang akan kadaluwarsa dalam 60 hari.");
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
