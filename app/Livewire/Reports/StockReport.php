<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Batch;
use App\Models\Product;

#[Layout('layouts.app')]
class StockReport extends Component
{
    use WithPagination;

    public $search = '';
    public $startExpiry = '';
    public $endExpiry = '';
    public $categoryFilter = '';
    public $stockStatus = 'all'; // all, low, out_of_stock

    protected $queryString = ['search', 'categoryFilter', 'stockStatus', 'startExpiry', 'endExpiry'];

    public function mount()
    {
        if (!auth()->user()->can('view stock')) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        $query = Batch::query()
            ->with(['product.category', 'product.unit'])
            ->where('stock_current', '>', 0)
            ->whereHas('product', function($q) {
                $q->where(function($sq) {
                    $sq->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('barcode', 'like', '%'.$this->search.'%');
                });
                
                if ($this->categoryFilter) {
                    $q->where('category_id', $this->categoryFilter);
                }
            });

        if ($this->startExpiry) {
            $query->whereDate('expired_date', '>=', $this->startExpiry);
        }

        if ($this->endExpiry) {
            $query->whereDate('expired_date', '<=', $this->endExpiry);
        }

        if ($this->stockStatus === 'low') {
            $query->whereHas('product', function($q) {
                $q->whereRaw('stock_current <= min_stock');
            });
        }

        $batches = (clone $query)->orderBy('expired_date')->paginate(15);

        $totalInventoryValue = (clone $query)
            ->selectRaw('SUM(batches.stock_current * batches.buy_price) as total_value')
            ->value('total_value');

        $totalStock = (clone $query)->sum('stock_current');

        return view('livewire.reports.stock-report', [
            'batches' => $batches,
            'totalInventoryValue' => $totalInventoryValue,
            'totalStock' => $totalStock,
            'categories' => \App\Models\Category::orderBy('name')->get(),
        ]);
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StockReportExport($this->search, $this->startExpiry, $this->endExpiry, $this->categoryFilter, $this->stockStatus), 
            'laporan-stok-'.date('Y-m-d').'.xlsx'
        );
    }
}
