<?php

namespace App\Livewire\Procurement;

use Livewire\Component;

class GoodsReceiptIndex extends Component
{
    use \Livewire\WithPagination;

    public $search = '';

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

        return view('livewire.procurement.goods-receipt-index', compact('receipts'));
    }
}
