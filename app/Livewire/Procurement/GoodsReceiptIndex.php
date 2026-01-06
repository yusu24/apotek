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
    public $payment_notes = '';
    public $remaining_debt = 0;

    public function mount()
    {
        if (!auth()->user()->can('view goods receipts')) {
            abort(403, 'Unauthorized');
        }
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
        $this->payment_notes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedId = null;
    }

    public function savePayment()
    {
        $this->validate([
            'payment_amount' => 'required|numeric|min:1|max:' . $this->remaining_debt,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer',
        ], [
            'payment_amount.max' => 'Jumlah bayar tidak boleh melebihi sisa hutang (Rp ' . number_format($this->remaining_debt, 0, ',', '.') . ')',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () {
            $payment = \App\Models\SupplierPayment::create([
                'goods_receipt_id' => $this->selectedId,
                'payment_date' => $this->payment_date,
                'amount' => $this->payment_amount,
                'payment_method' => $this->payment_method,
                'notes' => $this->payment_notes,
                'user_id' => auth()->id(),
            ]);

            $gr = \App\Models\GoodsReceipt::find($this->selectedId);
            $gr->updatePaymentStatus();

            // Accounting integration
            try {
                $accountingService = new \App\Services\AccountingService();
                $accountingService->postSupplierPaymentJournal($payment->id);
            } catch (\Exception $e) {
                \Log::error('Failed to post supplier payment journal: ' . $e->getMessage());
            }
        });

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
