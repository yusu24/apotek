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
        if (!auth()->user()->can('view sales reports')) {
            abort(403, 'Unauthorized action.');
        }
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedPeriod()
    {
        // Auto-adjust dates when period changes
        if ($this->period === 'daily') {
            $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $this->endDate = Carbon::now()->format('Y-m-d');
        } elseif ($this->period === 'monthly') {
            $this->startDate = Carbon::now()->subMonths(12)->format('Y-m-d');
            $this->endDate = Carbon::now()->format('Y-m-d');
        }
    }

    public function render()
    {
        $query = Sale::query();
        
        // Select & Grouping based on period
        if ($this->period === 'daily') {
            $data = $query->select(
                    DB::raw('DATE(created_at) as label'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->map(function($item) {
                    return [
                        'label' => Carbon::parse($item->label)->format('d M Y'),
                        'total' => $item->total,
                        'count' => $item->count,
                        'average' => $item->count > 0 ? $item->total / $item->count : 0,
                        'raw_date' => $item->label
                    ];
                });
        } elseif ($this->period === 'weekly') {
            $data = $query->select(
                    DB::raw('YEARWEEK(created_at, 1) as label'),
                    DB::raw('MIN(DATE(created_at)) as start_date'),
                    DB::raw('MAX(DATE(created_at)) as end_date'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subWeeks(12))
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->map(function($item) {
                     return [
                         'label' => 'Minggu ' . substr($item->label, 4) . ' (' . Carbon::parse($item->start_date)->format('d M') . ' - ' . Carbon::parse($item->end_date)->format('d M') . ')',
                         'total' => $item->total,
                         'count' => $item->count,
                         'average' => $item->count > 0 ? $item->total / $item->count : 0,
                         'raw_date' => $item->label
                     ];
                });
        } elseif ($this->period === 'monthly') {
            $data = $query->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as label'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->map(function($item) {
                    return [
                        'label' => Carbon::parse($item->label . '-01')->format('F Y'),
                        'total' => $item->total,
                        'count' => $item->count,
                        'average' => $item->count > 0 ? $item->total / $item->count : 0,
                        'raw_date' => $item->label
                    ];
                });
        } elseif ($this->period === 'yearly') {
            $data = $query->select(
                    DB::raw('YEAR(created_at) as label'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subYears(5))
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->map(function($item) {
                    return [
                        'label' => 'Tahun ' . $item->label,
                        'total' => $item->total,
                        'count' => $item->count,
                        'average' => $item->count > 0 ? $item->total / $item->count : 0,
                        'raw_date' => $item->label
                    ];
                });
        } elseif ($this->period === 'custom') {
            $data = $query->select(
                    DB::raw('DATE(created_at) as label'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereDate('created_at', '>=', $this->startDate)
                ->whereDate('created_at', '<=', $this->endDate)
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->map(function($item) {
                    return [
                        'label' => Carbon::parse($item->label)->format('d M Y'),
                        'total' => $item->total,
                        'count' => $item->count,
                        'average' => $item->count > 0 ? $item->total / $item->count : 0,
                        'raw_date' => $item->label
                    ];
                });
        }

        $dates = $data->pluck('label');
        $totals = $data->pluck('total');
        $counts = $data->pluck('count');
        $averages = $data->pluck('average');

        // Calculate summary statistics
        $totalRevenue = $totals->sum();
        $totalTransactions = $counts->sum();
        $overallAverage = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        return view('livewire.reports.sales-chart', [
            'dates' => $dates,
            'totals' => $totals,
            'counts' => $counts,
            'averages' => $averages,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'overallAverage' => $overallAverage,
            'data' => $data
        ]);
    }

    public function updated($propertyName)
    {
        // Dispatch event to update chart when any property changes
        $this->dispatch('chartDataUpdated');
    }
}
