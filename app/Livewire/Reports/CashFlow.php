<?php

namespace App\Livewire\Reports;

use App\Services\AccountingService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan Arus Kas')]
class CashFlow extends Component
{
    public $startDate;
    public $endDate;
    public $reportData = [];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->generateReport();
    }

    public function generateReport()
    {
        $accountingService = new AccountingService();
        $this->reportData = $accountingService->getCashFlowStatement($this->startDate, $this->endDate);
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
        $store = [
            'name' => \App\Models\Setting::get('store_name', config('app.name')),
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        return view('livewire.reports.cash-flow', [
            'store' => $store,
        ]);
    }
}
