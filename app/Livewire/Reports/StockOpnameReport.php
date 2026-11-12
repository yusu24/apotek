<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\StockMovement;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockOpnameReportExport;

#[Layout('layouts.app')]
class StockOpnameReport extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $varianceType = 'all'; // all, minus, plus
    public $startDate;
    public $endDate;
    public $perPage = 10;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'varianceType' => ['except' => 'all'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        if (!auth()->user()->can('view stock') && !auth()->user()->can('view stock movements')) {
            abort(403, 'Unauthorized');
        }

        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingVarianceType()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->varianceType = 'all';
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->perPage = 10;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
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
        $this->resetPage();
    }

    protected function buildBaseQuery()
    {
        $query = StockMovement::query()
            ->where('stock_movements.type', 'adjustment')
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->leftJoin('batches', 'batches.id', '=', 'stock_movements.batch_id');

        if ($this->startDate) {
            $query->whereDate('stock_movements.created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('stock_movements.created_at', '<=', $this->endDate);
        }

        if ($this->categoryFilter) {
            $query->where('products.category_id', $this->categoryFilter);
        }

        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', $search)
                  ->orWhere('products.barcode', 'like', $search)
                  ->orWhere('stock_movements.doc_ref', 'like', $search)
                  ->orWhere('stock_movements.description', 'like', $search)
                  ->orWhere('batches.batch_no', 'like', $search);
            });
        }

        return $query;
    }

    public static function getSummaryStats($query)
    {
        $unitPriceExpr = 'CASE WHEN COALESCE(batches.buy_price, 0) > 0 THEN batches.buy_price ELSE COALESCE(products.purchase_price, 0) END';

        $statsRow = (clone $query)
            ->selectRaw("
                COUNT(*) as total_records,
                COALESCE(SUM(CASE WHEN stock_movements.quantity < 0 THEN ABS(stock_movements.quantity) ELSE 0 END), 0) as minus_qty,
                COALESCE(SUM(CASE WHEN stock_movements.quantity < 0 THEN ABS(stock_movements.quantity) * ($unitPriceExpr) ELSE 0 END), 0) as minus_value,
                COUNT(DISTINCT CASE WHEN stock_movements.quantity < 0 THEN stock_movements.product_id END) as minus_items,
                COALESCE(SUM(CASE WHEN stock_movements.quantity > 0 THEN stock_movements.quantity ELSE 0 END), 0) as plus_qty,
                COALESCE(SUM(CASE WHEN stock_movements.quantity > 0 THEN stock_movements.quantity * ($unitPriceExpr) ELSE 0 END), 0) as plus_value,
                COUNT(DISTINCT CASE WHEN stock_movements.quantity > 0 THEN stock_movements.product_id END) as plus_items,
                COUNT(DISTINCT stock_movements.product_id) as total_items
            ")
            ->first();

        $minusQty = (int) ($statsRow->minus_qty ?? 0);
        $minusVal = (float) ($statsRow->minus_value ?? 0);
        $plusQty = (int) ($statsRow->plus_qty ?? 0);
        $plusVal = (float) ($statsRow->plus_value ?? 0);

        return [
            'total_records' => (int) ($statsRow->total_records ?? 0),
            'total_items' => (int) ($statsRow->total_items ?? 0),
            'minus_qty' => $minusQty,
            'minus_value' => $minusVal,
            'minus_items' => (int) ($statsRow->minus_items ?? 0),
            'plus_qty' => $plusQty,
            'plus_value' => $plusVal,
            'plus_items' => (int) ($statsRow->plus_items ?? 0),
            'net_qty' => $plusQty - $minusQty,
            'net_value' => $plusVal - $minusVal,
            'abs_qty' => $plusQty + $minusQty,
            'abs_value' => $plusVal + $minusVal,
        ];
    }

    public function exportExcel()
    {
        $filename = 'Laporan-Selisih-Stok-Opname-' . ($this->startDate ?: 'all') . '-sd-' . ($this->endDate ?: 'all') . '.xlsx';

        return Excel::download(
            new StockOpnameReportExport(
                $this->startDate,
                $this->endDate,
                $this->categoryFilter,
                $this->varianceType,
                $this->search
            ),
            $filename
        );
    }

    public function render()
    {
        $baseQuery = $this->buildBaseQuery();

        // Calculate summary cards (Plus, Minus, Total Variance)
        // If varianceType filter is active, we still want the filtered query for the table,
        // and we apply varianceType to stats as well if filtered, or compute overall from baseQuery.
        // Keeping stats aligned with baseQuery (date + category + search) ensures Stok Minus, Stok Plus, and Total Selisih are always visible side-by-side!
        $stats = self::getSummaryStats($baseQuery);

        $tableQuery = (clone $baseQuery)
            ->with(['product.category', 'product.unit', 'batch', 'user'])
            ->select('stock_movements.*')
            ->selectRaw('CASE WHEN COALESCE(batches.buy_price, 0) > 0 THEN batches.buy_price ELSE COALESCE(products.purchase_price, 0) END as unit_hpp');

        if ($this->varianceType === 'minus') {
            $tableQuery->where('stock_movements.quantity', '<', 0);
        } elseif ($this->varianceType === 'plus') {
            $tableQuery->where('stock_movements.quantity', '>', 0);
        }

        if ($this->sortBy === 'product') {
            $tableQuery->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortBy === 'quantity') {
            $tableQuery->orderBy('stock_movements.quantity', $this->sortDirection);
        } else {
            $tableQuery->orderBy('stock_movements.created_at', $this->sortDirection)
                       ->orderBy('stock_movements.id', $this->sortDirection);
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $movements */
        $movements = $tableQuery->paginate($this->perPage);
        $movements->onEachSide(1);

        return view('livewire.reports.stock-opname-report', [
            'movements' => $movements,
            'stats' => $stats,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
