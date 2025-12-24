<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductPerformance extends Component
{
    public $period = 'monthly'; // daily, weekly, monthly, yearly

    public function updatedPeriod()
    {
        $this->dispatch('chart-update');
    }

    public function getPerformanceData()
    {
        $startDate = match ($this->period) {
            'daily' => Carbon::now()->subDays(30),
            'weekly' => Carbon::now()->subWeeks(12),
            'monthly' => Carbon::now()->subMonths(12),
            'yearly' => Carbon::now()->subYears(5),
            default => Carbon::now()->subMonths(12),
        };

        // Top Selling
        $topSelling = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('sale', function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate)->where('status', 'completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(5)
            ->get();

        // Slowest Moving 
        $slowMoving = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
             ->whereHas('sale', function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate)->where('status', 'completed');
            })
            ->groupBy('product_id')
            ->orderBy('total_qty', 'asc')
            ->with('product')
            ->limit(5)
            ->get();

        return [
            'topSellingChart' => [
                'labels' => $topSelling->pluck('product.name')->toArray(),
                'data' => $topSelling->pluck('total_qty')->toArray(),
            ],
            'slowMovingChart' => [
                'labels' => $slowMoving->pluck('product.name')->toArray(),
                'data' => $slowMoving->pluck('total_qty')->toArray(),
            ],
            'period' => $this->period // Pass period to trigger updates if needed
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.product-performance', $this->getPerformanceData());
    }
}
