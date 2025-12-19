<?php

namespace App\Livewire\Reports;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app')]
class SalesChart extends Component
{

    public $period = 'daily'; // daily, weekly, monthly, yearly, custom
    public $startDate;
    public $endDate;

    public function mount()
    {
        if (!auth()->user()->can('view reports')) {
            abort(403, 'Unauthorized action.');
        }
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedPeriod()
    {
        if ($this->period !== 'custom') {
            // Reset logic if needed, or just let the query handle it
        }
    }

    public function render()
    {
        $query = Sale::query();
        
        // Select & Grouping based on period
        if ($this->period === 'daily') {
            $data = $query->select(
                    DB::raw('DATE(created_at) as label'),
                    DB::raw('SUM(grand_total) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        } elseif ($this->period === 'weekly') {
            $data = $query->select(
                    DB::raw('YEARWEEK(created_at) as label'), // MySQL specific, returns YYYYWW
                    DB::raw('MIN(DATE(created_at)) as start_date'), // Helper to show readable date
                    DB::raw('SUM(grand_total) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subWeeks(12))
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->map(function($item) {
                     return [
                         'label' => 'Minggu ' . substr($item->label, 4) . ' (' . Carbon::parse($item->start_date)->format('d M') . ')',
                         'total' => $item->total,
                         'raw_date' => $item->label
                     ];
                });
        } elseif ($this->period === 'monthly') {
            $data = $query->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as label'),
                    DB::raw('SUM(grand_total) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        } elseif ($this->period === 'yearly') {
            $data = $query->select(
                    DB::raw('YEAR(created_at) as label'),
                    DB::raw('SUM(grand_total) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subYears(5))
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        } elseif ($this->period === 'custom') {
            $data = $query->select(
                    DB::raw('DATE(created_at) as label'),
                    DB::raw('SUM(grand_total) as total')
                )
                ->whereDate('created_at', '>=', $this->startDate)
                ->whereDate('created_at', '<=', $this->endDate)
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        }
        
        // If not mapped cleanly yet (daily, monthly, yearly, custom)
        if ($this->period !== 'weekly') {
             $data = $data->map(function($item) {
                return [
                    'label' => $item->label,
                    'total' => $item->total,
                    'raw_date' => $item->label
                ];
             });
        }

        $dates = $data->pluck('label');
        $totals = $data->pluck('total');

        return view('livewire.reports.sales-chart', [
            'dates' => $dates,
            'totals' => $totals
        ]);
    }
}
