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
            ->where('created_at', '>=', $startDate)
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(5)
            ->get();

        // Slowest Moving (Non-zero sellers first, then others if needed)
        $slowMoving = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('product_id')
            ->orderBy('total_qty', 'asc')
            ->with('product')
            ->limit(5)
            ->get();

        return [
            'topSelling' => $topSelling,
            'slowMoving' => $slowMoving,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.product-performance', $this->getPerformanceData());
    }
}
