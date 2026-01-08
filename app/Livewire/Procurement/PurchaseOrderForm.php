<?php

namespace App\Livewire\Procurement;

use Livewire\Component;

class PurchaseOrderForm extends Component
{
    public $purchaseOrder;
    public $po_number;
    public $supplier_id;
    public $date;
    public $notes;
    public $status = 'draft';
    public $items = []; 
    public $suppliers = [];
    public $products = [];

    public $isReadOnly = false;

    // Modal Properties
    public $showModal = false;
    public $editingItemIndex = null;
    public $modalProductId = '';
    public $modalProductName = '';
    public $modalProductCode = '';
    public $modalQty = 1;
    public $modalUnit = '';
    public $modalPrice = null;
    public $modalSubtotal = 0;
    public $modalNotes = '';
    public $modalPpn = false;
    public $modalUnitId = null;
    public $modalConversionFactor = 1;
    public $availableUnits = [];
    public $modalSellPrice = 0;
    public $modalMargin = 0;
    public $modalMarginPercentage = 0;


    public function mount($id = null)
    {
        $this->suppliers = \App\Models\Supplier::all();
        $this->products = \App\Models\Product::with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->select('id', 'name', 'barcode', 'sell_price', 'unit_id')->get();

        if ($id) {
            $this->purchaseOrder = \App\Models\PurchaseOrder::with('items')->findOrFail($id);
            $this->po_number = $this->purchaseOrder->po_number;
            $this->supplier_id = $this->purchaseOrder->supplier_id;
            $this->date = $this->purchaseOrder->date;
            $this->notes = $this->purchaseOrder->notes;
            $this->status = $this->purchaseOrder->status;

            foreach ($this->purchaseOrder->items as $item) {
                $this->items[] = [
                    'product_id' => $item->product_id,
                    'qty' => $item->qty_ordered,
                    'unit_price' => $item->unit_price,
                    'has_ppn' => $item->has_ppn,
                    'subtotal' => $item->subtotal,
                    'unit_id' => $item->unit_id,
                    'conversion_factor' => $item->conversion_factor,
                ];
            }
            
            if ($this->status !== 'draft') {
                $this->isReadOnly = true;
            }
        } else {
            $this->date = date('Y-m-d');
            $this->po_number = 'PO-' . date('Ymd') . '-' . rand(100, 999);
            // No initial empty item needed if using modal
            // $this->items = []; 
        }
    }



    public function openModal($index = null)
    {
        $this->resetModal();
        $this->editingItemIndex = $index;

        if (!is_null($index) && isset($this->items[$index])) {
            $item = $this->items[$index];
            $this->modalProductId = $item['product_id'];
            $this->modalQty = $item['qty'];
            $this->modalPrice = $item['unit_price'];
            $this->modalPpn = $item['has_ppn'] ?? false;
            // Fetch product details for display
            $product = $this->products->firstWhere('id', $item['product_id']);
            if ($product) {
               $this->updatedModalProductId($item['product_id']); 
               $this->modalQty = $item['qty'];
               $this->modalPrice = $item['unit_price']; 
               $this->modalUnitId = $item['unit_id'] ?? $product->unit_id;
               $this->updatedModalUnitId($this->modalUnitId); // Refresh unit name/factor
               $this->modalConversionFactor = $item['conversion_factor'] ?? 1; 
            }
        }
        
        $this->calculateModalTotal();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    public function resetModal()
    {
        $this->editingItemIndex = null;
        $this->modalProductId = '';
        $this->modalProductName = '';
        $this->modalProductCode = '';
        $this->modalQty = 1;
        $this->modalUnit = '';
        $this->modalPrice = 0;
        $this->modalSubtotal = 0;
        $this->modalNotes = '';
        $this->modalPpn = false;
        $this->modalUnitId = null;
        $this->modalConversionFactor = 1;
        $this->availableUnits = [];
        $this->modalSellPrice = 0;
        $this->modalMargin = 0;
        $this->modalMarginPercentage = 0;
    }

    public function updatedModalProductId($value)
    {
        $product = $this->products->firstWhere('id', $value);
        if ($product) {
            $this->modalProductName = $product->name;
            $this->modalProductCode = $product->barcode;
            
            // Prepare available units: Base Unit + Conversions
            $this->availableUnits = [];
            
            // Base Unit (Smallest)
            if ($product->unit) {
                $this->availableUnits[] = [
                    'id' => $product->unit_id,
                    'name' => $product->unit->name,
                    'factor' => 1,
                ];
            }

            // Conversions (Larger Units) where to_unit_id is the base unit? 
            // Usually conversions are defined as "1 Box = 10 Pcs". 
            // If product unit is Pcs (to_unit), and we have conversion from Box (from_unit).
            // We need to check both directions or standard direction.
            // Assuming unitConversions stores: from_unit (Box), to_unit (Pcs), factor (10).
            foreach ($product->unitConversions as $conversion) {
                if ($conversion->to_unit_id == $product->unit_id) {
                    $this->availableUnits[] = [
                        'id' => $conversion->from_unit_id,
                        'name' => $conversion->fromUnit?->name ?? 'Unknown',
                        'factor' => $conversion->conversion_factor,
                    ];
                }
            }
            
            // Default to Base Unit (Smallest)
            $this->modalUnitId = null; // Clear first to trigger update if same
            $this->modalUnitId = $product->unit_id;
            $this->updatedModalUnitId($this->modalUnitId);

            // Set basic sell price from product (for base unit)
            // Actual comparison depends on the selected unit factor
            // updateModalUnitId will trigger calculateMargin via update
        } else {
            $this->modalProductName = '';
            $this->modalProductCode = '';
            $this->modalUnit = '';
            $this->availableUnits = [];
            $this->modalUnitId = null;
        }
    }

    public function updatedModalUnitId($value)
    {
        $selected = collect($this->availableUnits)->firstWhere('id', $value);
        if ($selected) {
            $this->modalUnit = $selected['name'];
            $this->modalConversionFactor = $selected['factor'];
        } else {
            $this->modalUnit = '-';
            $this->modalConversionFactor = 1;
        }
        $this->calculateMargin();
    }

    public function updatedModalQty()
    {
        $this->calculateModalTotal();
    }

    public function updatedModalPrice()
    {
        $this->calculateModalTotal();
        $this->calculateMargin();
    }
    
    public function updatedModalPpn()
    {
        $this->calculateModalTotal();
    }

    public function incrementQty()
    {
        $this->modalQty = (int)$this->modalQty + 1;
        $this->calculateModalTotal();
    }

    public function decrementQty()
    {
        $this->modalQty = max(1, (int)$this->modalQty - 1);
        $this->calculateModalTotal();
    }

    public function calculateModalTotal()
    {
        $total = (float)$this->modalQty * (float)$this->modalPrice;
        if ($this->modalPpn) {
            $ppnRate = (float) \App\Models\Setting::get('pos_ppn_rate', 11) / 100;
            $total = $total * (1 + $ppnRate);
        }
        $this->modalSubtotal = $total;
    }

    public function calculateMargin()
    {
        if (!$this->modalProductId) return;

        $product = $this->products->firstWhere('id', $this->modalProductId);
        if (!$product) return;

        // Calculate Sell Price for the selected unit
        // Base Sell Price * Conversion Factor
        $baseSellPrice = (float) $product->sell_price;
        $configuredSellPrice = $baseSellPrice * (float) $this->modalConversionFactor;
        
        $this->modalSellPrice = $configuredSellPrice;

        $buyPrice = (float) $this->modalPrice;

        if ($buyPrice > 0) {
            $this->modalMargin = $configuredSellPrice - $buyPrice;
            $this->modalMarginPercentage = ($this->modalMargin / $buyPrice) * 100;
        } else {
            $this->modalMargin = 0;
            $this->modalMarginPercentage = 0;
        }
    }

    public function saveItem()
    {
        $this->validate([
            'modalProductId' => 'required',
            'modalQty' => 'required|numeric|min:1',
        ]);

        $this->modalPrice = 0; // Ensure it's 0 if hidden
        $this->modalSubtotal = 0; // Ensure it's 0 if hidden

        $newItem = [
            'product_id' => $this->modalProductId,
            'qty' => $this->modalQty,
            'unit_price' => $this->modalPrice,
            'has_ppn' => $this->modalPpn,
            'subtotal' => $this->modalSubtotal,
            'unit_id' => $this->modalUnitId,
            'conversion_factor' => $this->modalConversionFactor,
        ];

        if (!is_null($this->editingItemIndex) && isset($this->items[$this->editingItemIndex])) {
            $this->items[$this->editingItemIndex] = $newItem;
        } else {
            $this->items[] = $newItem;
        }

        $this->closeModal();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function markAsDone()
    {
        if ($this->purchaseOrder && $this->purchaseOrder->status === 'partial') {
            $this->purchaseOrder->update(['status' => 'received']);
            session()->flash('message', 'PO status berhasil diubah menjadi Diterima (Selesai).');
            $this->redirect(route('procurement.purchase-orders.index'), navigate: true);
        }
    }

    public function save()
    {
        $this->validate([
            'po_number' => 'required|unique:purchase_orders,po_number,' . ($this->purchaseOrder?->id),
            'supplier_id' => 'required',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        $total_amount = collect($this->items)->sum('subtotal');

        if ($this->purchaseOrder) {
            $po = $this->purchaseOrder;
            $po->update([
                'supplier_id' => $this->supplier_id,
                'date' => $this->date,
                'status' => $this->status,
                'notes' => $this->notes,
                'total_amount' => $total_amount,
            ]);
            // Sync items (delete all and recreate is easiest for now)
            $po->items()->delete();
        } else {
            $po = \App\Models\PurchaseOrder::create([
                'po_number' => $this->po_number,
                'supplier_id' => $this->supplier_id,
                'user_id' => auth()->id(),
                'date' => $this->date,
                'status' => $this->status,
                'notes' => $this->notes,
                'total_amount' => $total_amount,
            ]);
        }

        foreach ($this->items as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'],
                'qty_ordered' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'has_ppn' => $item['has_ppn'] ?? false,
                'subtotal' => $item['subtotal'],
                'unit_id' => $item['unit_id'] ?? null,
                'conversion_factor' => $item['conversion_factor'] ?? 1,
            ]);
        }

        session()->flash('message', 'Pesanan pembelian berhasil disimpan.');
        $this->redirect(route('procurement.purchase-orders.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.procurement.purchase-order-form');
    }
}
