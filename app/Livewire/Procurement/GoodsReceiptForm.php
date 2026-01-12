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
    public $bank_account_id = null; // New Property
    public $items = []; // ['product_id', 'product_name', 'batch_no', 'expired_date', 'qty_received', 'buy_price']

    public $purchaseOrders = [];
    public $products = [];
    public $accounts = []; // New Property
    
    public $po_id; // For query string
    
    public $productSearch = '';
    public $searchResults = [];
    
    public $isEdit = false;
    public $receiptId;
    
    protected $queryString = ['po_id'];

    public function mount($id = null)
    {
        \Log::info('GoodsReceiptForm mount called with id: ' . $id . ' and po_id: ' . $this->po_id);
        
        $this->received_date = date('Y-m-d');
        $this->purchaseOrders = \App\Models\PurchaseOrder::whereIn('status', ['ordered', 'partial'])->get();
        $this->products = \App\Models\Product::with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->select('id', 'name', 'barcode', 'unit_id')->get();
        $this->accounts = \App\Models\Account::where('type', 'bank')->get(); // Load Bank Accounts
        
        if ($id) {
            $this->isEdit = true;
            $this->receiptId = $id;
            $this->loadReceipt($id);
        } elseif ($this->po_id) {
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

    public function loadReceipt($id)
    {
        $gr = \App\Models\GoodsReceipt::with(['items.product', 'items.unit'])->findOrFail($id);
        
        $this->purchase_order_id = $gr->purchase_order_id;
        $this->delivery_note_number = $gr->delivery_note_number;
        $this->received_date = $gr->received_date;
        $this->notes = $gr->notes;
        $this->payment_method = $gr->payment_method;
        $this->bank_account_id = $gr->bank_account_id;
        $this->due_date_weeks = $gr->due_date_weeks;
        
        $this->items = [];
        foreach ($gr->items as $item) {
            $poItem = null;
            if ($gr->purchase_order_id) {
                $poItem = \App\Models\PurchaseOrderItem::where('purchase_order_id', $gr->purchase_order_id)
                    ->where('product_id', $item->product_id)
                    ->first();
            }

            $this->items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'batch_no' => $item->batch_no,
                'expired_date' => $item->expired_date,
                'qty_received' => (float)$item->qty_received,
                'buy_price' => (float)$item->buy_price,
                'sell_price' => (float)($item->product->sell_price ?? 0), // Defaulting to current product sell price if not stored in item
                'margin' => (float)$item->buy_price > 0 ? ((($item->product->sell_price ?? 0) - $item->buy_price) / $item->buy_price) * 100 : 0,
                'unit_id' => $item->unit_id,
                'conversion_factor' => (float)($item->conversion_factor ?? 1),
                'po_item_id' => $poItem ? $poItem->id : null,
                'po_info' => $poItem ? 'Item PO (' . ($poItem->qty_ordered) . ' ' . ($poItem->unit->name ?? 'Unit') . ')' : null,
            ];
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
                                $totalReceivedBase += ((float)$grItem->qty_received * (float)($grItem->conversion_factor ?? 1));
                            }
                        }
                    }

                    $totalOrderedBase = (float)$poItem->qty_ordered * (float)($poItem->conversion_factor ?? 1);
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
                                'qty_received' => null, // Default to null as requested
                                'buy_price' => null, // Default to null as requested
                                'sell_price' => $poItem->product->sell_price ?? 0,
                                'margin' => 0,
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
            'qty_received' => null,
            'buy_price' => null,
            'sell_price' => 0,
            'margin' => 0,
            'unit_id' => null,
            'conversion_factor' => 1,
        ];
    }

    public function updatedProductSearch($value)
    {
        if (strlen($value) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = \App\Models\Product::where('name', 'like', '%' . $value . '%')
            ->orWhere('barcode', 'like', '%' . $value . '%')
            ->limit(10)
            ->get();
    }

    public function selectProduct($productId)
    {
        $product = \App\Models\Product::with(['unit', 'unitConversions'])->find($productId);
        
        if ($product) {
            $this->items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'batch_no' => $this->generateNextBatchNo(),
                'expired_date' => '',
                'qty_received' => null,
                'buy_price' => null,
                'sell_price' => $product->sell_price ?? 0,
                'margin' => 0,
                'unit_id' => $product->unit_id,
                'conversion_factor' => 1,
            ];
        }

        $this->productSearch = '';
        $this->searchResults = [];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updated($name, $value)
    {
        // Livewire 3 nested property update
        if (str_starts_with($name, 'items.')) {
            $parts = explode('.', $name);
            if (count($parts) == 3) {
                $index = $parts[1];
                $field = $parts[2];

                if ($field === 'product_id') {
                    $product = $this->products->firstWhere('id', $value);
                    if ($product) {
                        $this->items[$index]['unit_id'] = $product->unit_id;
                        $this->items[$index]['conversion_factor'] = 1;
                        $this->items[$index]['buy_price'] = null;
                        $this->items[$index]['sell_price'] = $product->sell_price ?? 0;
                        $this->calculateMargin($index);
                    }
                }

                if ($field === 'unit_id') {
                    $unitId = $value;
                    $productId = $this->items[$index]['product_id'] ?? null;
                    
                    if ($productId) {
                        $product = $this->products->firstWhere('id', $productId);
                        if ($product) {
                            $oldFactor = (float)($this->items[$index]['conversion_factor'] ?? 1);
                            $newFactor = 1;

                            if ($unitId == $product->unit_id) {
                                $newFactor = 1;
                            } else {
                                $conversion = $product->unitConversions->where('from_unit_id', $unitId)->first();
                                $newFactor = $conversion ? (float)$conversion->conversion_factor : 1;
                            }

                            // Adjust Qty and Prices based on factor change
                            if ($oldFactor > 0 && $newFactor > 0) {
                                $currentQty = (float)($this->items[$index]['qty_received'] ?? 0);
                                $currentBuy = (float)($this->items[$index]['buy_price'] ?? 0);
                                $currentSell = (float)($this->items[$index]['sell_price'] ?? 0);
                                
                                // Base values (per smallest unit)
                                $baseQty = $currentQty * $oldFactor;
                                $baseBuy = $currentBuy / $oldFactor;
                                $baseSell = $currentSell / $oldFactor;
                                
                                // New values (per selected unit)
                                $this->items[$index]['qty_received'] = (float)($baseQty / $newFactor);
                                $this->items[$index]['buy_price'] = $baseBuy * $newFactor;
                                $this->items[$index]['sell_price'] = $baseSell * $newFactor;
                            }

                            $this->items[$index]['conversion_factor'] = $newFactor;
                            $this->calculateMargin($index);
                        }
                    }
                }

                if ($field === 'buy_price' || $field === 'sell_price' || $field === 'qty_received') {
                    $this->calculateMargin($index);
                }
            }
        }
    }

    private function calculateMargin($index)
    {
        $buy = (float)($this->items[$index]['buy_price'] ?? 0);
        $sell = (float)($this->items[$index]['sell_price'] ?? 0);
        
        if ($buy > 0) {
            $this->items[$index]['margin'] = (($sell - $buy) / $buy) * 100;
        } else {
            $this->items[$index]['margin'] = 0;
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
            'bank_account_id' => 'required_if:payment_method,transfer',
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
            'bank_account_id.required_if' => 'Akun Bank wajib dipilih untuk Transfer',
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
                            $currentUnitName = \App\Models\Unit::find($item['unit_id'])?->name ?? 'Unit';
                            $this->addError("items.{$index}.qty_received", "Gagal: Jumlah terima (" . (float)$item['qty_received'] . " " . $currentUnitName . ") melebihi sisa pesanan di PO (" . (float)$item['max_qty_allowed'] . " " . $item['po_unit_name'] . ")");
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
            if ($this->isEdit) {
                $gr = \App\Models\GoodsReceipt::with(['items', 'journalEntries'])->findOrFail($this->receiptId);
                
                // 1. Revert Stock & Movements
                foreach ($gr->items as $oldItem) {
                    $baseQtyOld = $oldItem->qty_received * ($oldItem->conversion_factor ?? 1);
                    $batch = \App\Models\Batch::where('product_id', $oldItem->product_id)
                        ->where('batch_no', $oldItem->batch_no)
                        ->first();
                    
                    if ($batch) {
                        $batch->decrement('stock_in', $baseQtyOld);
                        $batch->decrement('stock_current', $baseQtyOld);
                    }
                }
                \App\Models\StockMovement::where('doc_ref', 'like', 'GR-' . $gr->id . '%')->delete();

                // 2. Clear previous Journal Entries (they will be re-posted)
                foreach ($gr->journalEntries as $je) {
                    $je->reverse();
                    $je->lines()->delete();
                    $je->delete();
                }

                // 3. Update main GR
                $gr->update([
                    'purchase_order_id' => $this->purchase_order_id ?: null,
                    'delivery_note_number' => $this->delivery_note_number,
                    'received_date' => $this->received_date,
                    'notes' => $this->notes,
                    'payment_method' => $this->payment_method,
                    'bank_account_id' => $this->payment_method === 'transfer' ? $this->bank_account_id : null,
                    'due_date_weeks' => $this->payment_method === 'due_date' ? $this->due_date_weeks : null,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'payment_status' => $paymentStatus,
                    'due_date' => $dueDate,
                ]);

                // 4. Update items (Delete old, create new for simplicity and to handle added/removed items)
                $gr->items()->delete();
            } else {
                // 1. Create Goods Receipt
                $gr = \App\Models\GoodsReceipt::create([
                    'purchase_order_id' => $this->purchase_order_id ?: null,
                    'delivery_note_number' => $this->delivery_note_number,
                    'received_date' => $this->received_date,
                    'user_id' => auth()->id(),
                    'notes' => $this->notes,
                    'payment_method' => $this->payment_method,
                    'bank_account_id' => $this->payment_method === 'transfer' ? $this->bank_account_id : null, 
                    'due_date_weeks' => $this->payment_method === 'due_date' ? $this->due_date_weeks : null,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'payment_status' => $paymentStatus,
                    'due_date' => $dueDate,
                ]);
            }

            foreach ($this->items as $item) {
                // 5. Create GR Item
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

                // 6. Update or Create Batch
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

                // 7. Log Movement
                \App\Models\StockMovement::create([
                    'product_id' => $item['product_id'],
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'quantity' => $baseQty,
                    'doc_ref' => 'GR-' . ($gr->id ?? '?') . ' (SJ: ' . ($this->delivery_note_number ?? '-') . ')',
                    'description' => ($this->isEdit ? '[EDIT] ' : '') . 'Penerimaan Barang dari ' . ($gr->purchaseOrder?->supplier?->name ?? 'Direct'),
                    'user_id' => auth()->id(),
                ]);

                // 8. Update Product Master Prices (Base Prices)
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $baseSellPrice = ($item['conversion_factor'] ?? 1) > 0 
                        ? $item['sell_price'] / ($item['conversion_factor'] ?? 1)
                        : $item['sell_price'];
                    
                    $oldData = $product->toArray();
                    $newData = array_merge($oldData, ['sell_price' => $baseSellPrice]);

                    if (abs((float)$product->sell_price - (float)$baseSellPrice) > 0.01) {
                        $product->update(['sell_price' => $baseSellPrice]);
                        
                        \App\Models\ActivityLog::log([
                            'action' => 'updated',
                            'module' => 'products',
                            'description' => "Penyesuaian harga jual via Penerimaan Barang (GR-{$gr->id})",
                            'old_values' => $oldData,
                            'new_values' => $newData,
                            'subject_id' => $product->id,
                            'subject_type' => \App\Models\Product::class,
                        ]);
                    }
                }
            }

            // 8. Update PO Status
            if ($this->purchase_order_id) {
                $po = \App\Models\PurchaseOrder::with(['items', 'goodsReceipts.items'])->find($this->purchase_order_id);
                
                if ($po) {
                    $allReceived = true;
                    foreach ($po->items as $poItem) {
                        $totalReceivedBase = 0;
                        foreach ($po->goodsReceipts as $otherGr) {
                             foreach ($otherGr->items as $grItem) {
                                if ($grItem->product_id == $poItem->product_id) {
                                    $totalReceivedBase += ((float)$grItem->qty_received * (float)($grItem->conversion_factor ?? 1));
                                }
                            }
                        }
                        
                        $totalOrderedBase = (float)$poItem->qty_ordered * (float)($poItem->conversion_factor ?? 1);
                        
                        // If received is less than ordered (with small float tolerance)
                        if (($totalOrderedBase - $totalReceivedBase) > 0.001) {
                            $allReceived = false;
                            break;
                        }
                    }

                    $po->update(['status' => $allReceived ? 'received' : 'partial']);
                }
            }

            // 7. Accounting Integration
            try {
                $accountingService = new \App\Services\AccountingService();
                $accountingService->postPurchaseJournal($gr->id);
            } catch (\Exception $e) {
                \Log::error('Failed to post purchase journal for GR-' . $gr->id . ': ' . $e->getMessage());
                // We don't throw the exception to avoid rolling back the transaction for accounting errors
                // as the inventory and operational records are already saved correctly.
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
