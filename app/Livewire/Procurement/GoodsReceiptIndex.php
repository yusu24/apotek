<?php

namespace App\Livewire\Procurement;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GoodsReceiptIndex extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $perPage = 10;
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'received_date'],
        'sortDirection' => ['except' => 'desc'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public $sortBy = 'received_date';
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

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public $showModal = false;
    public $showDetailModal = false;
    public $showPaymentModal = false;
    public $selectedId = null;

    // Payment Form
    public $payment_amount = 0;
    public $payment_date = '';
    public $payment_method = 'cash';
    public $bank_account_id = null; // New Property
    public $payment_notes = '';
    public $remaining_debt = 0;

    public $accounts = []; // New Property

    public function mount()
    {
        if (!auth()->user()->can('view goods receipts')) {
            abort(403, 'Unauthorized');
        }
        
        $this->accounts = \App\Models\Account::where('category', 'cash_bank')
            ->orWhere('sub_category', 'cash')
            ->active()
            ->get();
    }

    public function showDetail($id)
    {
        $this->selectedId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedId = null;
    }

    public function openPaymentModal($id)
    {
        $gr = \App\Models\GoodsReceipt::findOrFail($id);
        $this->selectedId = $id;
        $this->remaining_debt = $gr->total_amount - $gr->paid_amount;
        $this->payment_amount = $this->remaining_debt;
        $this->payment_date = date('Y-m-d');
        $this->payment_method = 'cash';
        $this->bank_account_id = null;
        $this->payment_notes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedId = null;
        $this->bank_account_id = null;
    }

    public function savePayment()
    {
        $this->validate([
            'payment_amount' => 'required|numeric|min:1|max:' . $this->remaining_debt,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer',
            'bank_account_id' => 'required_if:payment_method,transfer',
        ], [
            'payment_amount.max' => 'Jumlah bayar tidak boleh melebihi sisa hutang (Rp ' . number_format($this->remaining_debt, 0, ',', '.') . ')',
            'bank_account_id.required_if' => 'Harap pilih akun bank untuk metode Transfer.',
        ]);

        try {
            $accountingService = new \App\Services\AccountingService();
            $accountingService->processSupplierPayment($this->selectedId, [
                'amount' => $this->payment_amount,
                'payment_method' => $this->payment_method,
                'account_id' => $this->bank_account_id,
                'date' => $this->payment_date,
                'notes' => $this->payment_notes,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process supplier payment: ' . $e->getMessage());
            $this->addError('payment_amount', 'Gagal memproses pembayaran: ' . $e->getMessage());
            return;
        }

        $this->showPaymentModal = false;
        $this->selectedId = null;
        session()->flash('message', 'Pembayaran berhasil dicatat.');
    }

    
    public function updatingPerPage()
    {
        $this->resetPage();
    }
public function render()
    {
        $sortableColumns = [
            'received_date' => 'received_date',
            'delivery_note_number' => 'delivery_note_number',
            'payment_status' => 'payment_status',
            'total_amount' => 'total_amount',
        ];
        $orderColumn = $sortableColumns[$this->sortBy] ?? 'received_date';

        /** @var \Illuminate\Pagination\LengthAwarePaginator $receipts */
        $receipts = \App\Models\GoodsReceipt::with('purchaseOrder.supplier', 'user', 'items')
            ->where(function ($q) {
                $q->where('delivery_note_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('purchaseOrder.supplier', function ($q2) {
                      $q2->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('received_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('received_date', '<=', $this->dateTo);
            })
            ->orderBy($orderColumn, $this->sortDirection)
            ->paginate($this->perPage);
        $receipts->onEachSide(1);

        $selectedReceipt = null;
        if ($this->showDetailModal && $this->selectedId) {
            $selectedReceipt = \App\Models\GoodsReceipt::with(['items.product', 'items.unit', 'purchaseOrder.supplier', 'user'])->find($this->selectedId);
        }

        return view('livewire.procurement.goods-receipt-index', compact('receipts', 'selectedReceipt'));
    }
}
