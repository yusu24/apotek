<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\AccountingService;
use Carbon\Carbon;

#[Layout('layouts.app')]
class TrialBalance extends Component
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
        $this->reportData = $accountingService->getTrialBalance($this->startDate, $this->endDate);
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
        return view('livewire.reports.trial-balance');
    }
}
