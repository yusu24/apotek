<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Sale;

#[Layout('layouts.print')]
class Receipt extends Component
{
    public $sale;
    public $saleId;

    public function mount($id)
    {
        $this->sale = Sale::with(['saleItems.product', 'user'])->find($id);
        
        if (!$this->sale) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.pos.receipt');
    }
}
