<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Services\AccountingService;
use Carbon\Carbon;

class BalanceSheet extends Component
{
    public $startDate;
    public $endDate;
    public $reportData = [];

    public function mount()
    {
        if (!auth()->user()->can('view balance sheet')) {
            abort(403, 'Unauthorized');
        }

        // Default to current month
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        
        $this->generateReport();
    }

    public function generateReport()
    {
        $accountingService = new AccountingService();
        $this->reportData = $accountingService->getBalanceSheet($this->startDate, $this->endDate);
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['startDate', 'endDate'])) {
            $this->generateReport();
        }
    }

    public function setThisMonth()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->generateReport();
    }

    public function setLastMonth()
    {
        $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
        $this->generateReport();
    }

    public function setThisYear()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.reports.balance-sheet')->layout('layouts.app');
    }
}
