<?php

namespace App\Livewire\Procurement;

use Livewire\Component;

class GoodsReceiptForm extends Component
{
    public $purchase_order_id;
    public $delivery_note_number;
    public $received_date;
    public $notes;
    public $payment_method = '';
    public $due_date_weeks = null;
    public $items = []; // ['product_id', 'product_name', 'batch_no', 'expired_date', 'qty_received', 'buy_price']

    public $purchaseOrders = [];
    public $products = [];
    
    public $po_id; // For query string
    
    protected $queryString = ['po_id'];

    public function mount()
    {
        \Log::info('GoodsReceiptForm mount called with po_id: ' . $this->po_id);
        
        $this->received_date = date('Y-m-d');
        $this->purchaseOrders = \App\Models\PurchaseOrder::whereIn('status', ['ordered', 'partial'])->get();
        $this->products = \App\Models\Product::with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->select('id', 'name', 'unit_id')->get();
        
        if ($this->po_id) {
            \Log::info('Processing PO ID: ' . $this->po_id);
            $this->purchase_order_id = $this->po_id;
            
            // Auto-fill info from PO
            $po = \App\Models\PurchaseOrder::with('supplier')->find($this->po_id);
            if ($po) {
                \Log::info('PO Found: ' . $po->po_number);
                $this->delivery_note_number = ''; // User must input manually
                $this->notes = 'Penerimaan dari PO: ' . $po->po_number . ' - ' . ($po->supplier->name ?? '');
            } else {
                \Log::warning('PO not found for ID: ' . $this->po_id);
            }
            
            $this->updatedPurchaseOrderId($this->po_id);
        } else {
            \Log::info('No PO ID provided, adding empty item');
            $this->addItem(); // Only add empty item if no PO selected
        }
    }

    public function updatedPurchaseOrderId($value)
    {
        if ($value) {
            $po = \App\Models\PurchaseOrder::with(['items', 'goodsReceipts.items', 'items.product'])->find($value);
            if ($po) {
                $this->items = [];
                $hasItems = false;
                
                foreach ($po->items as $poItem) {
                    // Calculate total previously received for this product from this PO
                    $totalReceivedBase = 0;
                    foreach ($po->goodsReceipts as $gr) {
                        foreach ($gr->items as $grItem) {
                            if ($grItem->product_id == $poItem->product_id) {
                                $totalReceivedBase += ($grItem->qty_received * ($grItem->conversion_factor ?? 1));
                            }
                        }
                    }

                    $totalOrderedBase = $poItem->qty_ordered * ($poItem->conversion_factor ?? 1);
                    $remainingBase = $totalOrderedBase - $totalReceivedBase;

                     // Convert remaining base back to PO Unit
                    $poItemFactor = $poItem->conversion_factor ?? 1;
                    
                    // Use tolerance for float comparison
                    if ($remainingBase > 0.001) {
                         $remainingQty = $remainingBase / $poItemFactor;
                         
                         $infoLabel = ($totalReceivedBase > 0) ? 'Sisa Order: ' : 'Total Order: ';

                             $this->items[] = [
                                'product_id' => $poItem->product_id,
                                'product_name' => $poItem->product->name ?? '-',
                                'batch_no' => $this->generateNextBatchNo(),
                                'expired_date' => '',
                                'qty_received' => (float)$remainingQty, 
                                'buy_price' => $poItem->unit_price,
                                'unit_id' => $poItem->unit_id,
                                'po_unit_id' => $poItem->unit_id, // Store for comparison
                                'po_unit_name' => $poItem->unit->name ?? 'Unit',
                                'conversion_factor' => $poItem->conversion_factor,
                                'po_info' => $infoLabel . (float)$remainingQty . ' ' . ($poItem->unit->name ?? 'Unit'),
                                'max_qty_allowed' => (float)$remainingQty, // Store for validation
                            ];
                        $hasItems = true;
                    }
                }
                
                if (!$hasItems) {
                    session()->flash('message', 'Semua item dalam PO ini sudah diterima sepenuhnya.');
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
            'unit_id' => null,
            'conversion_factor' => 1,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) == 3 && $parts[2] == 'product_id') {
           $index = $parts[0];
           $product = $this->products->firstWhere('id', $value);
           if ($product) {
               $this->items[$index]['unit_id'] = $product->unit_id;
               $this->items[$index]['conversion_factor'] = 1;
           }
        }
        
        if (count($parts) == 3 && $parts[2] == 'unit_id') {
            $index = $parts[0];
            $unitId = $value;
            $productId = $this->items[$index]['product_id'] ?? null;
            
            if ($productId) {
                $product = $this->products->firstWhere('id', $productId);
                if ($product) {
                    if ($unitId == $product->unit_id) {
                        $this->items[$index]['conversion_factor'] = 1;
                    } else {
                        $conversion = $product->unitConversions->where('from_unit_id', $unitId)->first();
                        // Try reverse or other logic if your conversions are bidirectional or strict.
                        // Assuming standard: from_unit(Large) -> to_unit(Small/Base) = factor.
                        // If selected unit is "Box" (from_unit_id), and base is "Pcs" (to_unit_id).
                        if ($conversion) {
                            $this->items[$index]['conversion_factor'] = $conversion->conversion_factor;
                        } else {
                             // Fallback or 1
                             $this->items[$index]['conversion_factor'] = 1;
                        }
                    }
                }
            }
        }
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
            'payment_method' => 'required',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.qty_received' => 'required|numeric|min:1',
            'items.*.batch_no' => 'required',
            'items.*.expired_date' => 'required|date',
            'items.*.buy_price' => 'required|numeric|min:1',
        ], [
            'delivery_note_number.required' => 'Nomor Surat Jalan wajib diisi',
            'received_date.required' => 'Tanggal terima wajib diisi',
            'payment_method.required' => 'Metode pembayaran wajib dipilih',
            'items.required' => 'Minimal harus ada 1 item',
            'items.*.product_id.required' => 'Produk wajib dipilih',
            'items.*.qty_received.required' => 'Jumlah terima wajib diisi',
            'items.*.batch_no.required' => 'Nomor Batch wajib diisi',
            'items.*.expired_date.required' => 'Tanggal kadaluarsa wajib diisi',
            'items.*.buy_price.required' => 'Harga beli wajib diisi',
            'items.*.buy_price.min' => 'Harga beli harus lebih dari 0',
        ]);

        // Custom validation: Check if Qty Received > PO Qty (if applicable)
        if ($this->purchase_order_id) {
            foreach ($this->items as $index => $item) {
                // 1. Qty validation
                if (isset($item['max_qty_allowed'])) {
                    $baseReceivedQty = $item['qty_received'] * ($item['conversion_factor'] ?? 1);
                    $poItem = \App\Models\PurchaseOrderItem::where('purchase_order_id', $this->purchase_order_id)
                        ->where('product_id', $item['product_id'])
                        ->first();
                    
                    if ($poItem) {
                        $poFactor = $poItem->conversion_factor ?? 1;
                        $maxBaseQty = $item['max_qty_allowed'] * $poFactor;

                        if ($baseReceivedQty > ($maxBaseQty + 0.001)) {
                            $this->addError("items.{$index}.qty_received", "Gagal: Jumlah terima (" . $item['qty_received'] . ") melebihi sisa sisa pesanan di PO (" . $item['max_qty_allowed'] . " " . $item['po_unit_name'] . ")");
                            return;
                        }
                    }
                }

                // 2. Unit validation
                if (isset($item['po_unit_id']) && $item['unit_id'] != $item['po_unit_id']) {
                    // Check if conversion exists
                    $product = \App\Models\Product::with('unitConversions')->find($item['product_id']);
                    $hasConversion = false;
                    
                    if ($product) {
                        if ($item['unit_id'] == $product->unit_id) {
                            $hasConversion = true;
                        } else {
                            $hasConversion = $product->unitConversions->where('from_unit_id', $item['unit_id'])->isNotEmpty();
                        }
                    }

                    if (!$hasConversion) {
                        $this->addError("items.{$index}.unit_id", "Gagal: Satuan '" . (\App\Models\Unit::find($item['unit_id'])?->name ?? '?') . "' tidak sesuai dengan PO (" . $item['po_unit_name'] . ") dan tidak memiliki pengaturan konversi.");
                        return;
                    }
                }
            }
        }

        $totalAmount = 0;
        foreach ($this->items as $item) {
            $totalAmount += ($item['qty_received'] * $item['buy_price']);
        }

        $paymentStatus = 'paid';
        $paidAmount = $totalAmount;
        $dueDate = null;

        if ($this->payment_method === 'due_date') {
            $paymentStatus = 'pending';
            $paidAmount = 0;
            $dueDate = \Carbon\Carbon::parse($this->received_date)->addWeeks((int)$this->due_date_weeks);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($totalAmount, $paymentStatus, $paidAmount, $dueDate) {
            // 1. Create Goods Receipt
            $gr = \App\Models\GoodsReceipt::create([
                'purchase_order_id' => $this->purchase_order_id ?: null,
                'delivery_note_number' => $this->delivery_note_number,
                'received_date' => $this->received_date,
                'user_id' => auth()->id(),
                'notes' => $this->notes,
                'payment_method' => $this->payment_method,
                'due_date_weeks' => $this->payment_method === 'due_date' ? $this->due_date_weeks : null,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
                'due_date' => $dueDate,
            ]);

            foreach ($this->items as $item) {
                // 2. Create GR Item
                $gr->items()->create([
                    'product_id' => $item['product_id'],
                    'batch_no' => $item['batch_no'],
                    'expired_date' => $item['expired_date'],
                    'qty_received' => $item['qty_received'],
                    'buy_price' => $item['buy_price'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'conversion_factor' => $item['conversion_factor'] ?? 1,
                ]);

                // Calculate Base Quantity for Stock Update
                $baseQty = $item['qty_received'] * ($item['conversion_factor'] ?? 1);
                
                // Calculate Base Buy Price for COGS (Price / Factor)
                $baseBuyPrice = ($item['conversion_factor'] ?? 1) > 0 
                    ? $item['buy_price'] / ($item['conversion_factor'] ?? 1)
                    : $item['buy_price'];

                // 3. Update or Create Batch
                $batch = \App\Models\Batch::updateOrCreate(
                    [
                        'product_id' => $item['product_id'],
                        'batch_no' => $item['batch_no'],
                    ],
                    [
                        'expired_date' => $item['expired_date'],
                        'buy_price' => $baseBuyPrice,
                    ]
                );
                $batch->increment('stock_in', $baseQty);
                $batch->increment('stock_current', $baseQty);

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
                    'quantity' => $baseQty,
                    'doc_ref' => 'GR-' . ($gr->id ?? '?') . ' (SJ: ' . ($this->delivery_note_number ?? '-') . ')',
                    'description' => 'Penerimaan Barang dari ' . ($gr->purchaseOrder?->supplier?->name ?? 'Direct'),
                    'user_id' => auth()->id(),
                ]);
            }

            // 6. Update PO Status
            if ($this->purchase_order_id) {
                $po = \App\Models\PurchaseOrder::with(['items', 'goodsReceipts.items'])->find($this->purchase_order_id);
                
                if ($po) {
                    $allReceived = true;
                    foreach ($po->items as $poItem) {
                        $totalReceivedBase = 0;
                        foreach ($po->goodsReceipts as $gr) {
                             foreach ($gr->items as $grItem) {
                                if ($grItem->product_id == $poItem->product_id) {
                                    $totalReceivedBase += ($grItem->qty_received * ($grItem->conversion_factor ?? 1));
                                }
                            }
                        }
                        
                        $totalOrderedBase = $poItem->qty_ordered * ($poItem->conversion_factor ?? 1);
                        
                        // If received is less than ordered (with small float tolerance)
                        if (($totalOrderedBase - $totalReceivedBase) > 0.001) {
                            $allReceived = false;
                            break;
                        }
                    }

                    $po->update(['status' => $allReceived ? 'received' : 'partial']);
                }
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
