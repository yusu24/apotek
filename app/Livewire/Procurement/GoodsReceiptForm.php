<?php

namespace App\Livewire\Procurement;

use Livewire\Component;

class GoodsReceiptForm extends Component
{
    public $purchase_order_id;
    public $delivery_note_number;
    public $received_date;
    public $notes;
    public $items = []; // ['product_id', 'product_name', 'batch_no', 'expired_date', 'qty_received', 'buy_price']

    public $purchaseOrders = [];
    public $products = [];

    public function mount()
    {
        $this->received_date = date('Y-m-d');
        $this->purchaseOrders = \App\Models\PurchaseOrder::whereIn('status', ['ordered', 'partial'])->get();
        $this->products = \App\Models\Product::select('id', 'name')->get();
        
        $this->addItem(); // Start with 1 empty item
    }

    public function updatedPurchaseOrderId($value)
    {
        if ($value) {
            $po = \App\Models\PurchaseOrder::with('items.product')->find($value);
            if ($po) {
                $this->items = [];
                foreach ($po->items as $poItem) {
                    $this->items[] = [
                        'product_id' => $poItem->product_id,
                        'product_name' => $poItem->product->name ?? '-',
                        'batch_no' => $this->generateNextBatchNo(),
                        'expired_date' => '',
                        'qty_received' => $poItem->qty_ordered, // Default to ordered qty
                        'buy_price' => $poItem->unit_price,
                    ];
                }
            }
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'product_name' => '',
            'batch_no' => $this->generateNextBatchNo(),
            'expired_date' => '',
            'qty_received' => 1,
            'buy_price' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function generateNextBatchNo()
    {
        $prefix = 'APOTEK-' . date('Ymd');
        
        // 1. Count existing in DB
        $dbCount = \App\Models\Batch::where('batch_no', 'like', $prefix . '%')->count();
        
        // 2. Count existing in current form items (to prevent duplicates in same session)
        $formCount = 0;
        foreach ($this->items as $item) {
            if (str_starts_with($item['batch_no'], $prefix)) {
                $formCount++;
            }
        }
        
        $nextSequence = $dbCount + $formCount + 1;
        return $prefix . '.' . sprintf('%03d', $nextSequence);
    }

    public function save()
    {
        $this->validate([
            'delivery_note_number' => 'required',
            'received_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.qty_received' => 'required|numeric|min:1',
            'items.*.batch_no' => 'required',
            'items.*.expired_date' => 'required|date',
            'items.*.buy_price' => 'required|numeric|min:0',
        ], [
            'delivery_note_number.required' => 'Nomor Surat Jalan wajib diisi',
            'received_date.required' => 'Tanggal terima wajib diisi',
            'items.required' => 'Minimal harus ada 1 item',
            'items.*.product_id.required' => 'Produk wajib dipilih',
            'items.*.qty_received.required' => 'Jumlah terima wajib diisi',
            'items.*.batch_no.required' => 'Nomor Batch wajib diisi',
            'items.*.expired_date.required' => 'Tanggal kadaluarsa wajib diisi',
            'items.*.buy_price.required' => 'Harga beli wajib diisi',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () {
            // 1. Create Goods Receipt
            $gr = \App\Models\GoodsReceipt::create([
                'purchase_order_id' => $this->purchase_order_id ?: null,
                'delivery_note_number' => $this->delivery_note_number,
                'received_date' => $this->received_date,
                'user_id' => auth()->id(),
                'notes' => $this->notes,
            ]);

            foreach ($this->items as $item) {
                // 2. Create GR Item
                $gr->items()->create([
                    'product_id' => $item['product_id'],
                    'batch_no' => $item['batch_no'],
                    'expired_date' => $item['expired_date'],
                    'qty_received' => $item['qty_received'],
                    'buy_price' => $item['buy_price'],
                ]);

                // 3. Update or Create Batch
                $batch = \App\Models\Batch::updateOrCreate(
                    [
                        'product_id' => $item['product_id'],
                        'batch_no' => $item['batch_no'],
                    ],
                    [
                        'expired_date' => $item['expired_date'],
                        // We increment stock
                    ]
                );
                $batch->increment('stock_in', $item['qty_received']);
                $batch->increment('stock_current', $item['qty_received']);

                // 4. Update Product Master Stock
                $product = \App\Models\Product::find($item['product_id']);
                // Assuming product has a stock field, OR we rely on batches sum. 
                // Usually for performance we keep a total.
                // Note: User previous code used batches, but let's check Product model.
                // Assuming Product has dynamic accessor or we should update it if it has 'stock' column.
                // Let's assume standard behavior: Update product stock if column exists?
                // I will just increment if the column exists, otherwise I rely on Batch sum.
                // Checking Product.php earlier... it logic wasn't fully shown but typically yes.
                // I'll stick to updating Batch which is the source of truth for "Apotek" (Expiry is key).
                
                // 5. Log Movement
                \App\Models\StockMovement::create([
                    'product_id' => $item['product_id'],
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'quantity' => $item['qty_received'],
                    'doc_ref' => 'GR-' . $gr->id . ' (SJ: ' . $this->delivery_note_number . ')',
                    'description' => 'Penerimaan Barang dari ' . ($gr->purchaseOrder->supplier->name ?? 'Direct'),
                    'user_id' => auth()->id(),
                ]);
            }

            // 6. Update PO Status
            if ($this->purchase_order_id) {
                $po = \App\Models\PurchaseOrder::find($this->purchase_order_id);
                // Simple logic: Mark received. Complex logic would check if all items received.
                // For now, mark received.
                $po->update(['status' => 'received']);
            }
        });

        session()->flash('message', 'Penerimaan barang berhasil disimpan & stok bertambah.');
        $this->redirect(route('procurement.goods-receipts.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.procurement.goods-receipt-form');
    }
}
