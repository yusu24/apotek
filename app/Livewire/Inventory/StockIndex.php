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
    public $filter_status = 'all'; // all, expired, low_stock

    public function render()
    {
        $batches = Batch::with('product')
            ->when($this->search, function($q) {
                $q->whereHas('product', function($sq) {
                    $sq->where('name', 'like', '%'.$this->search.'%');
                })->orWhere('batch_no', 'like', '%'.$this->search.'%');
            })
            ->when($this->filter_status === 'expired', function($q) {
                $q->whereDate('expired_date', '<=', now());
            })
            ->orderBy('expired_date', 'asc')
            ->paginate(10)
            ->onEachSide(2);

        return view('livewire.inventory.stock-index', [
            'batches' => $batches,
            'low_stock_products' => Product::whereRaw('
                (SELECT SUM(stock_current) FROM batches WHERE product_id = products.id) <= min_stock
            ')->get(),
        ]);
    }
}
