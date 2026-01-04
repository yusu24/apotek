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
    public $paperSize;

    public function mount($id)
    {
        $this->sale = Sale::with(['saleItems.product', 'saleItems.unit', 'user'])->find($id);
        
        if (!$this->sale) {
            abort(404);
        }

        $this->paperSize = \App\Models\Setting::get('pos_paper_size', '58mm');
    }

    public function render()
    {
        return view('livewire.pos.receipt');
    }
}
