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

    public $search = '';
    public $type = 'all'; // all, sale, purchase, adjustment, return, return-supplier
    public $startDate;
    public $endDate;
    public $perPage = 50;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        if (!auth()->user()->can('view stock movements')) {
            abort(403, 'Unauthorized');
        }

        // Set default to last 30 days
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedType()
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->type = 'all';
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->perPage = 50;
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
    }

    public function getTransactionsProperty()
    {
        $query = StockMovement::with(['product', 'batch', 'user']);

        // Apply sorting
        if ($this->sortBy === 'product') {
            $query->join('products', 'products.id', '=', 'stock_movements.product_id')
                ->select('stock_movements.*')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortBy === 'created_at') {
            $query->orderBy('stock_movements.created_at', $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        if ($this->startDate) {
            $query->whereDate('stock_movements.created_at', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->whereDate('stock_movements.created_at', '<=', $this->endDate);
        }

        if ($this->type !== 'all') {
            if ($this->type === 'sale') {
                $query->whereIn('type', ['sale', 'return']); // Include customer returns in sales context? Or strictly sale? Code mostly uses 'sale'. 
                // Let's stick to exact type match if user wants specific.
                // But for "Penjualan", usually includes Sale.
                $query->where('type', 'sale');
            } elseif ($this->type === 'purchase') {
                $query->whereIn('type', ['in']); // 'in' is often purchase/receipt
            } elseif ($this->type === 'return') {
                $query->whereIn('type', ['return', 'return-supplier']);
            } else {
                $query->where('type', $this->type);
            }
        }

        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate($this->perPage);
    }

    public function exportPdf()
    {
        return redirect()->route('pdf.transaction-history', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'type' => $this->type,
            'search' => $this->search,
        ]);
    }

    public function render()
    {
        return view('livewire.reports.transaction-history', [
            'transactions' => $this->transactions,
        ]);
    }
}
