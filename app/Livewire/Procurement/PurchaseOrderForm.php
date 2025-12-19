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
    public $status = 'ordered';
    public $items = []; // [ ['product_id' => '', 'qty' => 1, 'unit_price' => 0, 'subtotal' => 0] ]

    public $suppliers = [];
    public $products = [];

    public function mount($id = null)
    {
        $this->suppliers = \App\Models\Supplier::all();
        $this->products = \App\Models\Product::select('id', 'name', 'sell_price')->get(); // Ideally use buy_price if available, using sell_price as placeholder or fetch latest cost

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
                    'subtotal' => $item->subtotal,
                ];
            }
        } else {
            $this->date = date('Y-m-d');
            $this->po_number = 'PO-' . date('Ymd') . '-' . rand(100, 999);
            $this->items = [
                ['product_id' => '', 'qty' => 1, 'unit_price' => 0, 'subtotal' => 0]
            ];
        }
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => '', 'qty' => 1, 'unit_price' => 0, 'subtotal' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        // $key like '0.product_id'
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'product_id') {
            // Find product buy price inference (optional)
            // For now, user manually enters price
        }

        if ($field === 'qty' || $field === 'unit_price') {
            $qty = (int) ($this->items[$index]['qty'] ?? 0);
            $price = (float) ($this->items[$index]['unit_price'] ?? 0);
            $this->items[$index]['subtotal'] = $qty * $price;
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
            'items.*.unit_price' => 'required|numeric|min:0',
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
                'subtotal' => $item['subtotal'],
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
