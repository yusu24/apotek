<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\StockMovement;

#[Layout('layouts.app')]
class TransactionHistory extends Component
{
    use WithPagination;

    public $searchProduct = '';
    public $selectedProducts = [];
    public $startDate;
    public $endDate;
    public $perPage = 50;

    public function mount()
    {
        if (!auth()->user()->can('view stock movements')) {
            abort(403, 'Unauthorized');
        }

        // Set default to last 30 days
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function selectProduct($productId)
    {
        $product = Product::find($productId);
        if ($product && !collect($this->selectedProducts)->contains('id', $productId)) {
            $this->selectedProducts[] = [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
            ];
        }
        $this->searchProduct = '';
        $this->resetPage();
    }

    public function removeProduct($productId)
    {
        $this->selectedProducts = collect($this->selectedProducts)
            ->filter(fn($p) => $p['id'] != $productId)
            ->values()
            ->all();
    }

    public function resetFilters()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->perPage = 50;
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getHistory($productId)
    {
        $query = StockMovement::where('product_id', $productId)
            ->with(['batch', 'user']);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->latest()->paginate($this->perPage, ['*'], 'page_' . $productId);
    }

    public function render()
    {
        $searchresults = [];
        if (strlen($this->searchProduct) > 2) {
            $searchresults = Product::where('name', 'like', '%'.$this->searchProduct.'%')
                ->orWhere('barcode', 'like', '%'.$this->searchProduct.'%')
                ->limit(5)
                ->get();
        }

        return view('livewire.reports.transaction-history', [
            'searchresults' => $searchresults,
        ]);
    }
}
