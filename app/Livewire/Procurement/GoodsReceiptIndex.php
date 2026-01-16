<?php

namespace App\Livewire\Procurement;

use Livewire\Component;

class GoodsReceiptIndex extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
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
        
        $this->accounts = \App\Models\Account::where('category', 'cash_bank')->active()->get();
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
            \Log::error('Failed to process supplier payment: ' . $e->getMessage());
            $this->addError('payment_amount', 'Gagal memproses pembayaran: ' . $e->getMessage());
            return;
        }

        $this->showPaymentModal = false;
        $this->selectedId = null;
        session()->flash('message', 'Pembayaran berhasil dicatat.');
    }

    public function render()
    {
        $receipts = \App\Models\GoodsReceipt::with('purchaseOrder.supplier', 'user', 'items')
            ->where('delivery_note_number', 'like', '%' . $this->search . '%')
            ->orWhereHas('purchaseOrder.supplier', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10)
            ->onEachSide(2);

        $selectedReceipt = null;
        if ($this->showDetailModal && $this->selectedId) {
            $selectedReceipt = \App\Models\GoodsReceipt::with(['items.product', 'items.unit', 'purchaseOrder.supplier', 'user'])->find($this->selectedId);
        }

        return view('livewire.procurement.goods-receipt-index', compact('receipts', 'selectedReceipt'));
    }
}
