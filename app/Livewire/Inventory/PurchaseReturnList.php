<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
use App\Models\Batch;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class PurchaseReturnList extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;

    // Form
    public $selectedSupplierId = '';
    public $returnItems = [];
    public $notes = '';
    
    // Selection helpers
    public $productSearch = '';
    public $foundBatches = [];

    protected $rules = [
        'selectedSupplierId' => 'required',
        'notes' => 'nullable|string',
        'returnItems.*.quantity' => 'numeric|min:0',
    ];

    public function updatedProductSearch()
    {
        if (strlen($this->productSearch) > 2) {
            $this->foundBatches = Batch::with('product')
                ->whereHas('product', function($q) {
                    $q->where('name', 'like', '%' . $this->productSearch . '%');
                })
                ->where('stock_current', '>', 0)
                ->limit(10)
                ->get();
        } else {
            $this->foundBatches = [];
        }
    }

    public function addBatchToReturn($batchId)
    {
        $batch = Batch::with('product')->find($batchId);
        if ($batch && !isset($this->returnItems[$batchId])) {
            $this->returnItems[$batchId] = [
                'batch_id' => $batch->id,
                'batch_no' => $batch->batch_no,
                'product_id' => $batch->product_id,
                'product_name' => $batch->product->name,
                'quantity' => 0,
                'max_quantity' => $batch->stock_current,
                'cost_price' => $batch->buy_price,
            ];
        }
        $this->productSearch = '';
        $this->foundBatches = [];
    }

    public function removeItem($batchId)
    {
        unset($this->returnItems[$batchId]);
    }

    public function openModal()
    {
        $this->reset(['selectedSupplierId', 'returnItems', 'notes', 'productSearch', 'foundBatches']);
        $this->showModal = true;
    }

    public function saveReturn()
    {
        if (!$this->selectedSupplierId) {
            $this->addError('selectedSupplierId', 'Pilih supplier terlebih dahulu.');
            return;
        }

        $itemsToProcess = array_filter($this->returnItems, function($item) {
            return $item['quantity'] > 0;
        });

        if (empty($itemsToProcess)) {
            $this->addError('returnItems', 'Minimal satu barang harus diretur.');
            return;
        }

        foreach ($itemsToProcess as $id => $item) {
            if ($item['quantity'] > $item['max_quantity']) {
                $this->addError("returnItems.{$id}.quantity", "Jumlah retur melebihi stok yang tersedia.");
                return;
            }
        }

        $totalReturnAmount = collect($itemsToProcess)->sum(fn($i) => (float)($i['quantity'] ?: 0) * (float)($i['cost_price'] ?: 0));

        DB::beginTransaction();
        try {
            $purchaseReturn = PurchaseReturn::create([
                'supplier_id' => $this->selectedSupplierId,
                'return_no' => 'PR-' . date('YmdHis'),
                'user_id' => auth()->id(),
                'total_amount' => $totalReturnAmount,
                'notes' => $this->notes,
            ]);

            foreach ($itemsToProcess as $batchId => $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $batchId,
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                ]);

                // Update Stock
                $batch = Batch::find($batchId);
                $batch->decrement('stock_current', $item['quantity']);

                // Record Stock Movement
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'batch_id' => $batchId,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference' => $purchaseReturn->return_no,
                    'notes' => 'Retur Pembelian ke Supplier',
                    'user_id' => auth()->id(),
                ]);
            }

            ActivityLog::log([
                'action' => 'created',
                'module' => 'purchase_returns',
                'description' => "Membuat retur pembelian: {$purchaseReturn->return_no}",
                'new_values' => $purchaseReturn->toArray()
            ]);

            DB::commit();
            session()->flash('message', 'Retur pembelian berhasil disimpan.');
            $this->reset(['showModal', 'selectedSupplierId', 'returnItems', 'notes']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $returns = PurchaseReturn::with('supplier', 'user')
            ->where('return_no', 'like', '%' . $this->search . '%')
            ->orWhereHas('supplier', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.inventory.purchase-return-list', [
            'returns' => $returns,
            'suppliers' => Supplier::all()
        ]);
    }
}
