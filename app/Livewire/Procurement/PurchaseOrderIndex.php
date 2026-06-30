<?php

namespace App\Livewire\Procurement;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PurchaseOrderIndex extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $perPage = 10;
    public $status = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'date'],
        'sortDirection' => ['except' => 'desc'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public $sortBy = 'date';
    public $sortDirection = 'desc';

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (!auth()->user()->can('view purchase orders')) {
            abort(403, 'Unauthorized');
        }
    }

    
    public function updatingPerPage()
    {
        $this->resetPage();
    }
public function render()
    {
        // Map sortable columns to actual DB columns
        $sortableColumns = [
            'date' => 'date',
            'po_number' => 'po_number',
            'status' => 'status',
        ];
        $orderColumn = $sortableColumns[$this->sortBy] ?? 'date';

        /** @var \Illuminate\Pagination\LengthAwarePaginator $orders */
        $orders = \App\Models\PurchaseOrder::with('supplier', 'user')
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->where(function($query) {
                $query->where('po_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('date', '<=', $this->dateTo);
            })
            ->orderBy($orderColumn, $this->sortDirection)
            ->paginate($this->perPage);
        $orders->onEachSide(1);

        return view('livewire.procurement.purchase-order-index', compact('orders'));
    }

    public function delete($id)
    {
        $po = \App\Models\PurchaseOrder::findOrFail($id);
        if ($po->goodsReceipts()->exists()) {
            session()->flash('error', 'Tidak dapat menghapus PO yang sudah ada penerimaan barang.');
            return;
        }
        $po->delete();
        session()->flash('message', 'Pesanan pembelian berhasil dihapus.');
    }
}
