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
    
    // Filter properties
    public $searchTerm = '';
    public $filterType = 'all';
    public $startDate = '';
    public $endDate = '';

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->product = Product::with(['category', 'unit'])->findOrFail($productId);
        
        $this->productBatches = Batch::where('product_id', $productId)
            ->where('stock_current', '>', 0)
            ->orderBy('expired_date')
            ->get();
    }

    public function getStockMovementsProperty()
    {
        $query = StockMovement::where('product_id', $this->productId)
            ->with(['batch', 'user']);

        // Filter by transaction type
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        // Filter by date range
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        // Search by batch number, reference, or description
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->whereHas('batch', function($batchQuery) {
                    $batchQuery->where('batch_no', 'like', '%' . $this->searchTerm . '%');
                })
                ->orWhere('doc_ref', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Order chronologically for running balance calculation
        $movements = $query->orderBy('created_at', 'asc')
                          ->orderBy('id', 'asc')
                          ->limit(100)
                          ->get();

        // Calculate running balance
        $runningBalance = 0;
        $movements->transform(function($movement) use (&$runningBalance) {
            $runningBalance += $movement->quantity; // quantity is already +/-
            $movement->running_balance = $runningBalance;
            return $movement;
        });

        return $movements;
    }

    public function resetFilters()
    {
        $this->searchTerm = '';
        $this->filterType = 'all';
        $this->startDate = '';
        $this->endDate = '';
    }

    public function render()
    {
        return view('livewire.inventory.stock-history');
    }
}
