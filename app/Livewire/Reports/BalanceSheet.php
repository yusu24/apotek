<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Services\AccountingService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class BalanceSheet extends Component
{
    public $asOfDate;
    public $reportData = [];

    public function mount()
    {
        if (!auth()->user()->can('view balance sheet')) {
            abort(403, 'Unauthorized');
        }

        // Default to end of current month
        $this->asOfDate = now()->endOfMonth()->format('Y-m-d');

        $this->generateReport();
    }

    public function generateReport()
    {
        $accountingService = new AccountingService();
        $this->reportData = $accountingService->getBalanceSheet(null, $this->asOfDate);
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'asOfDate') {
            $this->generateReport();
        }
    }

    public function setEndOfThisMonth()
    {
        $this->asOfDate = now()->endOfMonth()->format('Y-m-d');
        $this->generateReport();
    }

    public function setEndOfLastMonth()
    {
        $this->asOfDate = now()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
        $this->generateReport();
    }

    public function setEndOfThisYear()
    {
        $this->asOfDate = now()->endOfYear()->format('Y-m-d');
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.reports.balance-sheet');
    }
}
