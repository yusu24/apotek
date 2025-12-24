<?php

namespace App\Livewire\Procurement;

use Livewire\Component;

class GoodsReceiptIndex extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $showDetailModal = false;
    public $selectedId = null;

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
