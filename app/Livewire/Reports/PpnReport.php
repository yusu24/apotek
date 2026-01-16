<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Services\TaxService;
use Carbon\Carbon;

class PpnReport extends Component
{
    public $month;
    public $year;
    public $reportData = null;

    public function mount()
    {
        if (!auth()->user()->can('view ppn report')) {
            abort(403, 'Unauthorized');
        }

        $this->month = now()->month;
        $this->year = now()->year;
        $this->generateReport();
    }

    public function generateReport()
    {
        $taxService = new TaxService();
        $this->reportData = $taxService->getMonthlySummary($this->year, $this->month);
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['month', 'year'])) {
            $this->generateReport();
        }
    }

    public function exportPdf()
    {
        return redirect()->route('pdf.ppn-report', [
            'year' => $this->year,
            'month' => $this->month
        ]);
    }

    public function render()
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create(null, $i)->format('F');
        }

        $years = range(now()->year - 5, now()->year + 1);

        return view('livewire.reports.ppn-report', [
            'months' => $months,
            'years' => $years,
        ])->layout('layouts.app');
    }
}
