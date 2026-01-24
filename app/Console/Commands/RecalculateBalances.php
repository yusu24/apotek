<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AccountingService;

class RecalculateBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:recalculate-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all account balances from journal entry history';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting balance recalculation...');
        
        try {
            $service = new AccountingService();
            $service->recalculateAccountBalances();
            
            $this->info('Account balances have been successfully synchronized with the Journal entries.');
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
