<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Batch;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class StockAdjustment extends Component
{
    public $batch_id;
    public $product_id;
    public $product_name;
    public $batch_no;
    public $current_stock;
    public $adjustment_type = 'add'; // add or subtract
    public $quantity;
    public $description;
    public $success_message = '';

    public function mount($batchId)
    {
        // Check permission - Super Admin only
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'Hanya Super Admin yang dapat menyesuaikan stok.');
        }

        $batch = Batch::with('product')->findOrFail($batchId);
        $this->batch_id = $batch->id;
        $this->product_id = $batch->product_id;
        $this->product_name = $batch->product->name;
        $this->batch_no = $batch->batch_no;
        $this->current_stock = $batch->stock_current;
    }

    public function save()
    {
        $this->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string|min:3',
        ]);

        DB::beginTransaction();
        try {
            $batch = Batch::findOrFail($this->batch_id);
            
            // Calculate new stock
            if ($this->adjustment_type === 'add') {
                $newStock = $batch->stock_current + $this->quantity;
                $movementQty = $this->quantity;
            } else {
                if ($this->quantity > $batch->stock_current) {
                    $this->addError('quantity', 'Jumlah pengurangan tidak boleh lebih dari stok saat ini.');
                    return;
                }
                $newStock = $batch->stock_current - $this->quantity;
                $movementQty = -$this->quantity;
            }

            // Update batch stock
            $batch->update(['stock_current' => $newStock]);

            // Record stock movement
            StockMovement::create([
                'product_id' => $this->product_id,
                'batch_id' => $this->batch_id,
                'user_id' => auth()->id(),
                'type' => 'adjustment',
                'quantity' => $movementQty,
                'doc_ref' => 'ADJ-' . now()->format('YmdHis'),
                'description' => $this->description,
            ]);

            DB::commit();

            $this->success_message = 'Stok berhasil disesuaikan!';
            $this->current_stock = $newStock;
            $this->reset(['quantity', 'description']);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('save', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inventory.stock-adjustment');
    }
}
