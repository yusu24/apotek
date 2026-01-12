<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\PurchaseReturn;

class PurchaseReturnPrint extends Component
{
    public $return;

    public function mount($id)
    {
        $this->return = PurchaseReturn::with(['supplier', 'user', 'items.product.unit', 'items.batch'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.inventory.purchase-return-print', [
            'return' => $this->return
        ])->layout('layouts.app'); 
    }
}
