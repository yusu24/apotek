<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ProcurementItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductMarginExport;
use Carbon\Carbon;

class ProductMarginReport extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $marginFilter = 'all'; // all, positive, negative, high (>30%), low (<10%)
    public $reportMode = 'potential'; // potential, realized
    public $startDate;
    public $endDate;
    public $month; // Keep for backward compatibility/legacy ref if needed, but primarily using dates now
    public $year;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'marginFilter' => ['except' => 'all'],
        'reportMode' => ['except' => 'potential'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        if (!auth()->user()->can('view product margin report')) {
            abort(403, 'Unauthorized');
        }

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->month = now()->format('m');
        $this->year = now()->format('Y');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedReportMode()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedYear()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
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

    public function getBaseQueryProperty()
    {
        if ($this->reportMode === 'realized') {
            return $this->getRealizedQuery();
        }

        return $this->getPotentialQuery();
    }

    private function getPotentialQuery()
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
                    products.purchase_price,
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
                    products.purchase_price,
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
                    products.purchase_price,
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
                    products.purchase_price,
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
                    products.purchase_price,
                    0
                ) * 100) 
                ELSE 0 
                END as margin_percentage
            ');

        $this->applyFilters($query);
        $this->applySorting($query);

        return $query;
    }

    private function getRealizedQuery()
    {
        $query = Product::with(['category', 'unit'])
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('batches', 'sale_items.batch_id', '=', 'batches.id')
            ->whereBetween('sales.date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->select([
                'products.id',
                'products.name',
                'products.barcode',
                'products.category_id',
                'products.unit_id',
            ])
            ->selectRaw('SUM(sale_items.quantity) as total_sold')
            ->selectRaw('AVG(sale_items.sell_price) as avg_sell_price')
            ->selectRaw('AVG(batches.buy_price) as avg_buy_price')
            ->selectRaw('SUM((sale_items.sell_price - batches.buy_price) * sale_items.quantity) as margin_amount')
            ->selectRaw('
                CASE 
                WHEN SUM(batches.buy_price * sale_items.quantity) > 0 
                THEN (SUM((sale_items.sell_price - batches.buy_price) * sale_items.quantity) / SUM(batches.buy_price * sale_items.quantity) * 100) 
                ELSE 0 
                END as margin_percentage
            ')
            ->groupBy('products.id', 'products.name', 'products.barcode', 'products.category_id', 'products.unit_id');

        $this->applyFilters($query, 'realized');
        $this->applySorting($query, 'realized');

        return $query;
    }

    private function applyFilters($query, $mode = 'potential')
    {
        if ($this->search) {
            $query->where(function($q) {
                $q->where('products.name', 'like', '%' . $this->search . '%')
                  ->orWhere('products.barcode', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('products.category_id', $this->categoryFilter);
        }

        if ($this->marginFilter === 'positive') {
            $query->havingRaw('margin_amount > 0');
        } elseif ($this->marginFilter === 'negative') {
            $query->havingRaw('margin_amount < 0');
        } elseif ($this->marginFilter === 'high') {
            $query->havingRaw('margin_percentage > 30');
        } elseif ($this->marginFilter === 'low') {
            $query->havingRaw('margin_percentage < 10 AND margin_percentage >= 0');
        }
    }

    private function applySorting($query, $mode = 'potential')
    {
        $direction = $this->sortDirection;
        
        if ($this->sortBy === 'margin_amount') {
            $query->orderByRaw('margin_amount ' . $direction);
        } elseif ($this->sortBy === 'margin_percentage') {
            $query->orderByRaw('margin_percentage ' . $direction);
        } elseif ($this->sortBy === 'last_buy_price' || $this->sortBy === 'avg_buy_price') {
            $query->orderByRaw(($mode === 'potential' ? 'last_buy_price ' : 'avg_buy_price ') . $direction);
        } elseif ($this->sortBy === 'sell_price' || $this->sortBy === 'avg_sell_price') {
            $query->orderByRaw(($mode === 'potential' ? 'products.sell_price ' : 'avg_sell_price ') . $direction);
        } else {
            $query->orderBy('products.' . $this->sortBy, $direction);
        }
    }

    public function getStatisticsProperty()
    {
        $products = $this->baseQuery->get();
        
        return [
            'total_products' => $products->count(),
            'products_with_positive_margin' => $products->where('margin_amount', '>', 0)->count(),
            'products_with_negative_margin' => $products->where('margin_amount', '<', 0)->count(),
            'average_margin_percentage' => $products->count() > 0 ? $products->avg('margin_percentage') : 0,
            'total_margin_value' => $products->sum('margin_amount'),
        ];
    }

    public function exportExcel()
    {
        $products = $this->baseQuery->get();
        
        return Excel::download(
            new ProductMarginExport($products, $this->reportMode, $this->startDate, $this->endDate), 
            'laporan-margin-produk-' . ($this->reportMode === 'realized' ? $this->startDate . '_ke_' . $this->endDate : date('Y-m-d')) . '.xlsx'
        );
    }

    public function render()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        
        $products = $this->baseQuery->paginate($this->perPage);
        $products->onEachSide(1);

        return view('livewire.reports.product-margin-report', [
            'products' => $products,
            'categories' => $categories,
            'statistics' => $this->statistics,
        ]);
    }
}
