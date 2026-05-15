<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Batch;
use App\Models\Product;

use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class StockIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    
    #[Url]
    public $filter_status = 'all'; // all, low_stock

    public $highlightIndex = 0;
    
    public function updatedSearch()
    {
        $this->resetPage();
        $this->highlightIndex = 0;
    }

    public function getSearchResultsProperty()
    {
        $query = Product::query();

        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('barcode', 'like', $search);
            });
        }

        return $query->orderBy('name', 'asc')->take(10)->get();
    }

    public function selectProductByIndex($index)
    {
        $searchResults = $this->searchResults;
        if (!empty($searchResults) && isset($searchResults[$index])) {
            $this->search = $searchResults[$index]->name;
            $this->resetPage();
        }
    }

    public function selectProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $this->search = $product->name;
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filter_status = 'all';
        $this->highlightIndex = 0;
        $this->resetPage();
    }

    public function mount()
    {
        if (!auth()->user()->can('view stock')) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        /** @var \Illuminate\Pagination\LengthAwarePaginator $products */
        $products = Product::query()
            ->with(['category', 'unit', 'batches' => function($q) {
                $q->orderBy('expired_date');
            }])
            ->withSum('batches as total_stock', 'stock_current')
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('barcode', 'like', '%'.$this->search.'%');
            })
            ->when($this->filter_status === 'low_stock', function($q) {
                $q->whereRaw('(select coalesce(sum(stock_current), 0) from batches where batches.product_id = products.id) <= products.min_stock');
            })
            ->orderBy('name')
            ->paginate($this->perPage);
        $products->onEachSide(1);

        return view('livewire.inventory.stock-index', [
            'products' => $products,
            'searchResults' => $this->searchResults,
            'low_stock_products' => Product::query()
                ->whereRaw('(select coalesce(sum(stock_current), 0) from batches where batches.product_id = products.id) <= products.min_stock')
                ->get(),
        ]);
    }
    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StockExport, 'Stok-Opname-' . date('d-m-Y') . '.xlsx');
    }
}
