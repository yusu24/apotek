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

    public $period = 'active_month'; // active_month, daily, weekly, monthly, yearly, custom
    public $startDate;
    public $endDate;

    protected $queryString = [
        'period' => ['except' => 'active_month'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function mount()
    {
        if (!auth()->user()->can('view sales reports')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Defaults based on period if not specified in query string
        if ($this->period === 'active_month') {
            if (!$this->startDate) {
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            }
            if (!$this->endDate) {
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
            }
        } else {
            if (!$this->startDate) {
                $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            }
            if (!$this->endDate) {
                $this->endDate = Carbon::now()->format('Y-m-d');
            }
        }
    }

    public function updatedPeriod()
    {
        // Auto-adjust dates when period changes
        if ($this->period === 'active_month') {
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($this->period === 'daily') {
            $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $this->endDate = Carbon::now()->format('Y-m-d');
        } elseif ($this->period === 'weekly') {
            $this->startDate = Carbon::now()->subWeeks(12)->format('Y-m-d');
            $this->endDate = Carbon::now()->format('Y-m-d');
        } elseif ($this->period === 'monthly') {
            $this->startDate = Carbon::now()->subMonths(12)->format('Y-m-d');
            $this->endDate = Carbon::now()->format('Y-m-d');
        } elseif ($this->period === 'yearly') {
            $this->startDate = Carbon::now()->subYears(5)->format('Y-m-d');
            $this->endDate = Carbon::now()->format('Y-m-d');
        }
    }

    public function render()
    {
        // Build query using 'date' to align with SalesReport.php
        $query = Sale::query()
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate);
        
        // Select & Grouping based on period
        if ($this->period === 'active_month' || $this->period === 'daily' || $this->period === 'custom') {
            $data = (clone $query)->select(
                    DB::raw('DATE(date) as label'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
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
            $data = (clone $query)->select(
                    DB::raw('YEARWEEK(date, 1) as label'),
                    DB::raw('MIN(DATE(date)) as start_date'),
                    DB::raw('MAX(DATE(date)) as end_date'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
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
            $data = (clone $query)->select(
                    DB::raw('DATE_FORMAT(date, "%Y-%m") as label'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
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
            $data = (clone $query)->select(
                    DB::raw('YEAR(date) as label'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
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
        }

        $dates = $data->pluck('label');
        $totals = $data->pluck('total');
        $counts = $data->pluck('count');
        $averages = $data->pluck('average');

        // Calculate statistics matching SalesReport.php
        $totalReturns = \App\Models\SalesReturn::whereBetween('created_at', [
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        ])->sum('total_amount');

        $stats = [
            'total_sales' => (clone $query)->sum('grand_total'),
            'total_returns' => $totalReturns,
            'net_sales' => (clone $query)->sum('dpp') - $totalReturns,
            'transaction_count' => (clone $query)->count(),
        ];

        // Backwards compatible variables for table footer
        $totalRevenue = $stats['total_sales'];
        $totalTransactions = $stats['transaction_count'];
        $overallAverage = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        return view('livewire.reports.sales-chart', [
            'dates' => $dates,
            'totals' => $totals,
            'counts' => $counts,
            'averages' => $averages,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'overallAverage' => $overallAverage,
            'stats' => $stats,
            'data' => $data
        ]);
    }

    public function updated($propertyName)
    {
        $this->dispatch('chartDataUpdated');
    }
}
