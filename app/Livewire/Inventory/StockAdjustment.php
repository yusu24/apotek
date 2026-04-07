<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ActivityLog;
use App\Models\Batch;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class StockAdjustment extends Component
{
    public $product_id;
    public $product_name;
    public $current_stock;
    public $adjustment_type = 'add'; // add or subtract
    public $quantity;
    public $description;
    public $success_message = '';

    public function mount($productId)
    {
        // Check permission - anyone with 'adjust stock' permission
        if (!auth()->user()->can('adjust stock')) {
            abort(403, 'Anda tidak memiliki akses untuk menyesuaikan stok.');
        }

        $product = Product::withSum('batches as total_stock', 'stock_current')->findOrFail($productId);
        $this->product_id = $product->id;
        $this->product_name = $product->name;
        $this->current_stock = $product->total_stock ?? 0;
    }

    public function save()
    {
        $this->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($this->product_id);
            $remainingToAdjust = $this->quantity;
            $movements = [];

            if ($this->adjustment_type === 'add') {
                // ADD LOGIC: Find latest batch or create one
                $batch = Batch::where('product_id', $this->product_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$batch) {
                    $batch = Batch::create([
                        'product_id' => $this->product_id,
                        'batch_no' => 'ADJ-' . now()->format('Ymd'),
                        'stock_in' => 0,
                        'stock_current' => 0,
                        'expired_date' => now()->addYear(),
                        'buy_price' => 0
                    ]);
                }

                $batch->stock_current += $remainingToAdjust;
                $batch->save();

                $movements[] = [
                    'batch_id' => $batch->id,
                    'qty' => $remainingToAdjust
                ];

            } else {
                // SUBTRACT LOGIC (FIFO)
                if ($this->quantity > $this->current_stock) {
                    $this->addError('quantity', 'Jumlah pengurangan tidak boleh lebih dari total stok saat ini.');
                    return;
                }

                $batches = Batch::where('product_id', $this->product_id)
                    ->where('stock_current', '>', 0)
                    ->orderBy('expired_date', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingToAdjust <= 0) break;

                    $take = min($batch->stock_current, $remainingToAdjust);
                    $batch->stock_current -= $take;
                    $batch->save();

                    $movements[] = [
                        'batch_id' => $batch->id,
                        'qty' => -$take
                    ];

                    $remainingToAdjust -= $take;
                }
            }

            // Record stock movements
            $docRef = 'ADJ-' . now()->format('YmdHis');
            foreach ($movements as $m) {
                StockMovement::create([
                    'product_id' => $this->product_id,
                    'batch_id' => $m['batch_id'],
                    'user_id' => auth()->id(),
                    'type' => 'adjustment',
                    'quantity' => $m['qty'],
                    'doc_ref' => $docRef,
                    'description' => $this->description ?: 'Penyesuaian stok total',
                ]);
            }

            DB::commit();

            $newTotal = Product::where('id', $this->product_id)->withSum('batches as total_stock', 'stock_current')->first()->total_stock;

            ActivityLog::log([
                'action' => 'updated',
                'module' => 'stock',
                'description' => "Penyesuaian stok total ({$this->adjustment_type}) untuk {$this->product_name}: {$this->quantity} unit.",
                'new_values' => [
                    'product_id' => $this->product_id,
                    'type' => $this->adjustment_type,
                    'quantity' => $this->quantity,
                    'old_total_stock' => $this->current_stock,
                    'new_total_stock' => $newTotal,
                    'description' => $this->description
                ]
            ]);

            session()->flash('message', 'Stok berhasil disesuaikan secara keseluruhan!');
            return $this->redirect(route('inventory.index'), navigate: true);

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
