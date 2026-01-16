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
    
    #[Url]
    public $filter_status = 'all'; // all, low_stock

    public function mount()
    {
        if (!auth()->user()->can('view stock')) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
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
            ->paginate(10);

        return view('livewire.inventory.stock-index', [
            'products' => $products,
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
