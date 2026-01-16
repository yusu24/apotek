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
    public $productSearch = '';
    public $highlightIndex = 0;

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

        if ($id) {
            $this->purchaseOrder = \App\Models\PurchaseOrder::with('items')->findOrFail($id);
            $this->po_number = $this->purchaseOrder->po_number;
            $this->supplier_id = $this->purchaseOrder->supplier_id;
            $this->date = \Carbon\Carbon::parse($this->purchaseOrder->date)->format('d/m/Y');
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
            $this->date = date('d/m/Y');
            $this->po_number = 'PO-' . date('Ymd') . '-' . rand(100, 999);
            // No initial empty item needed if using modal
            // $this->items = []; 
        }
    }



    public function openModal($index = null, $productId = null)
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
            $product = \App\Models\Product::with(['unit', 'unitConversions.fromUnit'])->find($item['product_id']);
            if ($product) {
               $this->updatedModalProductId($item['product_id']); 
               $this->modalQty = $item['qty'];
               $this->modalPrice = $item['unit_price']; 
               $this->modalUnitId = $item['unit_id'] ?? $product->unit_id;
               $this->updatedModalUnitId($this->modalUnitId); // Refresh unit name/factor
               $this->modalConversionFactor = $item['conversion_factor'] ?? 1; 
            }
        } elseif ($productId) {
            $productId = (int) $productId;
            $this->modalProductId = $productId;
            $this->updatedModalProductId($productId);
            $this->productSearch = ''; // Clear search
            $this->highlightIndex = 0;
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
        $product = \App\Models\Product::with(['unit', 'unitConversions.fromUnit'])->find((int) $value);
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
        $this->modalPrice = 0;
        $this->modalSubtotal = 0;
    }

    public function calculateMargin()
    {
        $this->modalMargin = 0;
        $this->modalMarginPercentage = 0;
    }

    public function saveItem()
    {
        $this->validate([
            'modalProductId' => 'required',
            'modalQty' => 'required|numeric|min:1',
        ]);

        $newItem = [
            'product_id' => $this->modalProductId,
            'qty' => $this->modalQty,
            'unit_price' => 0,
            'has_ppn' => false,
            'subtotal' => 0,
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
        // Convert dd/mm/yyyy to Y-m-d for validation and storage
        $dateForDb = $this->date;
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $this->date, $matches)) {
            $dateForDb = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        $this->validate([
            'po_number' => 'required|unique:purchase_orders,po_number,' . ($this->purchaseOrder?->id),
            'supplier_id' => 'required',
            'date' => 'required',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        $total_amount = collect($this->items)->sum('subtotal');

        if ($this->purchaseOrder) {
            $po = $this->purchaseOrder;
            $po->update([
                'supplier_id' => $this->supplier_id,
                'date' => $dateForDb,
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
                'date' => $dateForDb,
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

    public function updatedProductSearch()
    {
        $this->highlightIndex = 0;
    }

    public function incrementHighlight()
    {
        $count = count($this->searchResults);

        if ($this->highlightIndex < $count - 1) {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlight()
    {
        if ($this->highlightIndex > 0) {
            $this->highlightIndex--;
        }
    }

    public function selectHighlighted()
    {
        $searchResults = $this->searchResults;

        if (!empty($searchResults) && isset($searchResults[$this->highlightIndex])) {
            $this->openModal(null, $searchResults[$this->highlightIndex]->id);
        }
    }

    public function getSearchResultsProperty()
    {
        if (empty($this->productSearch)) {
            return [];
        }

        $search = '%' . $this->productSearch . '%';
        return \App\Models\Product::where(function ($q) use ($search) {
            $q->where('name', 'like', $search)
              ->orWhere('barcode', 'like', $search);
        })->take(10)->get();
    }

    public function render()
    {
        $productIds = collect($this->items)->pluck('product_id')->unique();
        $tableProducts = \App\Models\Product::whereIn('id', $productIds)->with(['unit', 'unitConversions.fromUnit'])->get();

        $modalProductData = null;
        if ($this->modalProductId) {
            $modalProductData = \App\Models\Product::select('id', 'name', 'barcode')->find($this->modalProductId);
        }

        return view('livewire.procurement.purchase-order-form', [
            'tableProducts' => $tableProducts,
            'searchResults' => $this->searchResults,
            'modalProductData' => $modalProductData
        ]);
    }
}
