<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProcurementItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductMarginExport;

class ProductMarginReport extends Component
{
    public $search = '';
    public $categoryFilter = '';
    public $marginFilter = 'all'; // all, positive, negative, high (>30%), low (<10%)
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'categoryFilter', 'marginFilter'];

    public function mount()
    {
        if (!auth()->user()->can('view product margin report')) {
            abort(403, 'Unauthorized');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getProductsWithMarginProperty()
    {
        $query = Product::with(['category', 'unit'])
            ->select([
                'products.id',
                'products.name',
                'products.barcode',
                'products.sell_price',
                'products.category_id',
                'products.unit_id',
            ])
            ->selectRaw('
                COALESCE(
                    (SELECT AVG(buy_price) 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     AND batches.stock_current > 0
                    ), 
                    (SELECT buy_price 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     ORDER BY created_at DESC 
                     LIMIT 1
                    ),
                    0
                ) as last_buy_price
            ')
            ->selectRaw('
                (products.sell_price - COALESCE(
                    (SELECT AVG(buy_price) 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     AND batches.stock_current > 0
                    ), 
                    (SELECT buy_price 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     ORDER BY created_at DESC 
                     LIMIT 1
                    ),
                    0
                )) as margin_amount
            ')
            ->selectRaw('
                CASE 
                WHEN COALESCE(
                    (SELECT AVG(buy_price) 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     AND batches.stock_current > 0
                    ), 
                    (SELECT buy_price 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     ORDER BY created_at DESC 
                     LIMIT 1
                    ),
                    0
                ) > 0 
                THEN ((products.sell_price - COALESCE(
                    (SELECT AVG(buy_price) 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     AND batches.stock_current > 0
                    ), 
                    (SELECT buy_price 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     ORDER BY created_at DESC 
                     LIMIT 1
                    ),
                    0
                )) / COALESCE(
                    (SELECT AVG(buy_price) 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     AND batches.stock_current > 0
                    ), 
                    (SELECT buy_price 
                     FROM batches 
                     WHERE batches.product_id = products.id 
                     ORDER BY created_at DESC 
                     LIMIT 1
                    ),
                    0
                ) * 100) 
                ELSE 0 
                END as margin_percentage
            ');

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('products.name', 'like', '%' . $this->search . '%')
                  ->orWhere('products.barcode', 'like', '%' . $this->search . '%');
            });
        }

        // Category Filter
        if ($this->categoryFilter) {
            $query->where('products.category_id', $this->categoryFilter);
        }

        // Margin Filter
        if ($this->marginFilter === 'positive') {
            $query->havingRaw('margin_amount > 0');
        } elseif ($this->marginFilter === 'negative') {
            $query->havingRaw('margin_amount < 0');
        } elseif ($this->marginFilter === 'high') {
            $query->havingRaw('margin_percentage > 30');
        } elseif ($this->marginFilter === 'low') {
            $query->havingRaw('margin_percentage < 10 AND margin_percentage >= 0');
        }

        // Sorting
        if ($this->sortBy === 'margin_amount') {
            $query->orderByRaw('margin_amount ' . $this->sortDirection);
        } elseif ($this->sortBy === 'margin_percentage') {
            $query->orderByRaw('margin_percentage ' . $this->sortDirection);
        } elseif ($this->sortBy === 'last_buy_price') {
            $query->orderByRaw('last_buy_price ' . $this->sortDirection);
        } elseif ($this->sortBy === 'sell_price') {
            $query->orderBy('products.sell_price', $this->sortDirection);
        } else {
            $query->orderBy('products.' . $this->sortBy, $this->sortDirection);
        }

        return $query->get();
    }

    public function getStatisticsProperty()
    {
        $products = $this->productsWithMargin;
        
        return [
            'total_products' => $products->count(),
            'products_with_positive_margin' => $products->where('margin_amount', '>', 0)->count(),
            'products_with_negative_margin' => $products->where('margin_amount', '<', 0)->count(),
            'average_margin_percentage' => $products->where('last_buy_price', '>', 0)->avg('margin_percentage'),
            'total_margin_value' => $products->sum('margin_amount'),
        ];
    }

    public function exportExcel()
    {
        $products = $this->productsWithMargin;
        
        return Excel::download(
            new ProductMarginExport($products), 
            'laporan-margin-produk-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        
        return view('livewire.reports.product-margin-report', [
            'products' => $this->productsWithMargin,
            'categories' => $categories,
            'statistics' => $this->statistics,
        ]);
    }
}
