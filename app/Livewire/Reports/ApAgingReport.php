<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Services\AccountingService;

class ApAgingReport extends Component
{
    public $reportData = null;
    public $activeTab = 'all'; // all, 0-30, 31-60, 61-90, >90

    public function mount()
    {
        if (!auth()->user()->can('view ap aging report')) {
            abort(403, 'Unauthorized');
        }

        $this->generateReport();
    }

    public function generateReport()
    {
        $accountingService = new AccountingService();
        $this->reportData = $accountingService->getApAgingReport();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function exportPdf()
    {
        return redirect()->route('pdf.ap-aging-report');
    }

    public function render()
    {
        return view('livewire.reports.ap-aging-report')->layout('layouts.app');
    }
}
