<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Batch;
use App\Models\StockMovement;

#[Layout('layouts.app')]
class StockHistory extends Component
{
    public $productId;
    public $product;
    public $productBatches = [];
    public $stockMovements = [];

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->product = Product::with(['category', 'unit'])->findOrFail($productId);
        
        $this->productBatches = Batch::where('product_id', $productId)
            ->where('stock_current', '>', 0)
            ->orderBy('expired_date')
            ->get();

        $this->stockMovements = StockMovement::where('product_id', $productId)
            ->with(['batch', 'user'])
            ->latest()
            ->limit(100)
            ->get();
    }

    public function render()
    {
        return view('livewire.inventory.stock-history');
    }
}
