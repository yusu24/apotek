<?php

namespace App\Livewire\Procurement;

use Livewire\Attributes\Layout;
use App\Models\PurchaseOrder;
use Livewire\Component;

#[Layout('layouts.print')]
class PurchaseOrderPrint extends Component
{
    public $po;

    public function mount($id)
    {
        $this->po = PurchaseOrder::with(['items.product', 'supplier', 'user'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.procurement.purchase-order-print');
    }
}
