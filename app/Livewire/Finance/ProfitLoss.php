<?php

namespace App\Livewire\Finance;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ProfitLossService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

#[Layout('layouts.app')]
class ProfitLoss extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $period = 'this_month'; // this_month, last_month, this_year, custom
    public $perPage = 10;

    public function mount()
    {
        // Permission check
        // Permission check
        if (!auth()->user()->can('view profit loss')) {
             abort(403, 'Unauthorized');
        }

        $this->updateDates();
    }

    public function updatedPeriod()
    {
        $this->updateDates();
        $this->resetPage('salesPage');
        $this->resetPage('cogsPage');
        $this->resetPage('expensePage');
        $this->resetPage('taxPage');
    }

    public function updatedStartDate()
    {
        $this->resetPage('salesPage');
        $this->resetPage('cogsPage');
        $this->resetPage('expensePage');
        $this->resetPage('taxPage');
    }

    public function updatedEndDate()
    {
        $this->resetPage('salesPage');
        $this->resetPage('cogsPage');
        $this->resetPage('expensePage');
        $this->resetPage('taxPage');
    }

    public function updatedPerPage()
    {
        $this->resetPage('salesPage');
        $this->resetPage('cogsPage');
        $this->resetPage('expensePage');
        $this->resetPage('taxPage');
    }

    private function paginateCollection($items, $perPage, $pageName)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        return new LengthAwarePaginator(
            $currentItems,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }

    public function updateDates()
    {
        $now = Carbon::now();
        if ($this->period === 'this_month') {
            $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
            $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
        } elseif ($this->period === 'last_month') {
            $this->startDate = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
            $this->endDate = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
        } elseif ($this->period === 'this_year') {
            $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
            $this->endDate = $now->copy()->endOfYear()->format('Y-m-d');
        }
    }

    public function exportExcel($compare = true)
    {
        $service = new ProfitLossService();
        $current = $service->calculateMetrics($this->startDate, $this->endDate);

        $previous = null;
        $prevStart = null;
        $prevEnd = null;
        if ($compare) {
            [$prevStart, $prevEnd] = $service->getPreviousPeriod($this->startDate, $this->endDate);
            $previous = $service->calculateMetrics($prevStart, $prevEnd);
        }

        $filename = 'Laporan_Laba_Rugi_Detail_' . $this->startDate . '_to_' . $this->endDate . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DetailedProfitLossExport(
                $current,
                $previous,
                $this->startDate,
                $this->endDate,
                $prevStart,
                $prevEnd,
                \App\Models\Setting::get('store_name', 'Apotek'),
                $compare
            ),
            $filename
        );
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = (new ProfitLossService())->calculateMetrics($this->startDate, $this->endDate);

        // Paginate collections for the view
        $data['salesDetails'] = $this->paginateCollection($data['salesDetails'], $this->perPage, 'salesPage');
        $data['cogsDetails'] = $this->paginateCollection($data['cogsDetails'], $this->perPage, 'cogsPage');
        $data['expenseDetails'] = $this->paginateCollection($data['expenseDetails'], $this->perPage, 'expensePage');
        $data['taxDetails'] = $this->paginateCollection($data['taxDetails'], $this->perPage, 'taxPage');

        return view('livewire.finance.profit-loss', $data);
    }
}
