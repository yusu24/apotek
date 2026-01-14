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
use App\Services\AccountingService;

class PurchaseReturnList extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;

    // Form
    public $selectedSupplierId = '';
    public $returnItems = [];
    public $notes = '';
    
    public $goodsReceipts = [];
    public $selectedGoodsReceiptId = '';
    
    public $showDetailModal = false;
    public $selectedReturn;

    protected $rules = [
        'selectedSupplierId' => 'required',
        'selectedGoodsReceiptId' => 'required',
        'notes' => 'nullable|string',
    ];

    public function updatedSelectedSupplierId()
    {
        // Fetch GRs based on Purchase Orders for the selected supplier
        $poIds = \App\Models\PurchaseOrder::where('supplier_id', $this->selectedSupplierId)->pluck('id');
        $this->goodsReceipts = \App\Models\GoodsReceipt::whereIn('purchase_order_id', $poIds)
            ->latest()
            ->get();
            
        $this->selectedGoodsReceiptId = '';
        $this->returnItems = [];
    }

    public function updatedSelectedGoodsReceiptId()
    {
        $gr = \App\Models\GoodsReceipt::with('items.product')->find($this->selectedGoodsReceiptId);
        $this->returnItems = [];

        if ($gr) {
            foreach ($gr->items as $item) {
                // Ensure batch stock is available
                // We use the batch_no from GR item to find the current batch status
                // But wait, GR Item stores batch_no.
                // We need the actual Batch model to check current stock.
                $batch = \App\Models\Batch::where('product_id', $item->product_id)
                    ->where('batch_no', $item->batch_no)
                    ->first();
                
                if ($batch) {
                    $this->returnItems[$batch->id] = [
                        'selected' => false,
                        'batch_id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'product_id' => $batch->product_id,
                        'product_name' => optional($item->product)->name ?? 'Produk Dihapus',
                        'quantity' => 0,
                        'max_quantity' => $batch->stock_current, // Limit by current stock
                        'gr_quantity' => $item->qty_received, // Info only
                        'cost_price' => $batch->buy_price,
                        'unit_name' => optional($item->unit)->name ?? 'pcs',
                    ];
                }
            }
        }
    }

    public function openModal()
    {
        $this->reset(['selectedSupplierId', 'selectedGoodsReceiptId', 'returnItems', 'notes', 'goodsReceipts']);
        $this->showModal = true;
    }

    public function viewDetails($id)
    {
        $this->selectedReturn = PurchaseReturn::with(['items.product', 'items.batch', 'supplier', 'user'])->find($id);
        $this->showDetailModal = true;
    }

    public function saveReturn()
    {
        $this->validate();

        $itemsToProcess = array_filter($this->returnItems, function($item) {
            return !empty($item['selected']) && $item['quantity'] > 0;
        });

        if (empty($itemsToProcess)) {
            $this->addError('returnItems', 'Pilih minimal satu barang dan masukkan jumlah retur.');
            return;
        }

        foreach ($itemsToProcess as $batchId => $item) {
            if ($item['quantity'] > $item['max_quantity']) {
                $this->addError("returnItems", "Jumlah retur untuk {$item['product_name']} melebihi stok tersedia ({$item['max_quantity']}).");
                return;
            }
        }

        $totalReturnAmount = collect($itemsToProcess)->sum(fn($i) => (float)($i['quantity'] ?: 0) * (float)($i['cost_price'] ?: 0));

        DB::beginTransaction();
        try {
            // Get Supplier Info for log or whatever, defined by selectedSupplierId
            
            $purchaseReturn = PurchaseReturn::create([
                'supplier_id' => $this->selectedSupplierId,
                // 'goods_receipt_id' => $this->selectedGoodsReceiptId, // Column does not exist in table
                'return_no' => 'PR-' . date('YmdHis'),
                'user_id' => auth()->id(),
                'total_amount' => $totalReturnAmount,
                'notes' => $this->notes . "\nRef SJ: " . (\App\Models\GoodsReceipt::find($this->selectedGoodsReceiptId)->delivery_note_number ?? '-'),
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
                $batch = \App\Models\Batch::find($batchId);
                $batch->decrement('stock_current', $item['quantity']);

                // Record Stock Movement
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'batch_id' => $batchId,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference' => $purchaseReturn->return_no,
                    'doc_ref' => 'PR-' . $purchaseReturn->id, 
                    'description' => 'Retur Pembelian (SJ: ' . (\App\Models\GoodsReceipt::find($this->selectedGoodsReceiptId)->delivery_note_number ?? '-') . ')',
                    'user_id' => auth()->id(),
                ]);
            }

            ActivityLog::log([
                'action' => 'created',
                'module' => 'purchase_returns',
                'description' => "Membuat retur pembelian: {$purchaseReturn->return_no}",
                'new_values' => $purchaseReturn->toArray()
            ]);

            // Create Journal Entry
            try {
                $accountingService = new AccountingService();
                $accountingService->postPurchaseReturnJournal($purchaseReturn->id);
            } catch (\Exception $e) {
                \Log::error('Failed to post purchase return journal: ' . $e->getMessage());
                // Don't fail the transaction, just log the error
            }

            DB::commit();
            session()->flash('message', 'Retur pembelian berhasil disimpan.');
            $this->showModal = false;
            $this->reset(['selectedSupplierId', 'selectedGoodsReceiptId', 'returnItems', 'notes']);
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
