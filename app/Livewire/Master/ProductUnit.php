<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Validation\Rule;

class ProductUnit extends Component
{
    use WithPagination;

    public $search = '';
    
    // Modal State
    public $showModal = false;
    public $editingProduct = null;
    
    // Form Data
    public $base_unit_id;
    public $conversions = []; // Array of ['id' => null, 'from_unit_id' => '', 'to_unit_id' => '', 'conversion_factor' => '']

    public function render()
    {
        $products = Product::query()
            ->with(['unit', 'category'])
            ->withCount('unitConversions') // Assuming relationship exists or we add it
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.master.product-unit', [
            'products' => $products,
            'units' => Unit::orderBy('name')->get(),
        ])->layout('layouts.app');
    }

    public function edit($productId)
    {
        $this->editingProduct = Product::with('unitConversions')->findOrFail($productId);
        $this->base_unit_id = $this->editingProduct->unit_id;
        
        // Load existing conversions
        $this->conversions = $this->editingProduct->unitConversions->map(function ($c) {
            return [
                'id' => $c->id,
                'from_unit_id' => $c->from_unit_id,
                'conversion_factor' => (float)$c->conversion_factor,
            ];
        })->toArray();

        $this->showModal = true;
    }

    public function addConversion()
    {
        $this->conversions[] = [
            'id' => null,
            'from_unit_id' => '',
            'conversion_factor' => '',
        ];
    }

    public function removeConversion($index)
    {
        unset($this->conversions[$index]);
        $this->conversions = array_values($this->conversions);
    }

    public function save()
    {
        $this->validate([
            'base_unit_id' => 'required|exists:units,id',
            'conversions.*.from_unit_id' => 'required|exists:units,id|different:base_unit_id',
            'conversions.*.conversion_factor' => 'required|numeric|min:0.0001',
        ]);

        // Validate duplicates in conversions
        $fromUnits = array_column($this->conversions, 'from_unit_id');
        if (count($fromUnits) !== count(array_unique($fromUnits))) {
            $this->addError('conversions', 'Terdapat duplikasi satuan konversi.');
            return;
        }

        // Update Base Unit
        $this->editingProduct->update(['unit_id' => $this->base_unit_id]);

        // Sync Conversions
        // Strategy: Delete all old permissions for this product and re-create/update? 
        // Better: smart sync. But valid approach for simplicity:
        // We need to keep IDs if possible for other references, but for now simple sync.
        
        // Let's do a robust sync
        $existingIds = collect($this->conversions)->pluck('id')->filter()->toArray();
        
        // Delete removed
        UnitConversion::where('product_id', $this->editingProduct->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        foreach ($this->conversions as $conv) {
            UnitConversion::updateOrCreate(
                [
                    'id' => $conv['id'],
                    'product_id' => $this->editingProduct->id,
                ],
                [
                    'from_unit_id' => $conv['from_unit_id'],
                    'to_unit_id' => $this->base_unit_id, // Always map to base
                    'conversion_factor' => $conv['conversion_factor'],
                ]
            );
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Pengaturan satuan berhasil disimpan.');
    }

    public function delete($productId)
    {
        // Delete all conversions
        UnitConversion::where('product_id', $productId)->delete();
        
        $this->dispatch('notify', 'Pengaturan konversi satuan berhasil direset.');
    }
}
