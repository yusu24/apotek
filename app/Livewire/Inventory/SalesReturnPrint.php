<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\SalesReturn;

class SalesReturnPrint extends Component
{
    public $return;

    public function mount($id)
    {
        $this->return = SalesReturn::with(['sale', 'user', 'items.product.unit', 'items.batch'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.inventory.sales-return-print', [
            'return' => $this->return
        ])->layout('layouts.app');
    }
}
