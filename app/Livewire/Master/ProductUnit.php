<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Category;
use App\Models\UnitConversion;
use Illuminate\Validation\Rule;

class ProductUnit extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    
    // Modal State
    public $showModal = false;
    public $editingProduct = null;
    
    // Form Data
    public $base_unit_id;
    public $conversions = []; // Array of ['id' => null, 'from_unit_id' => '', 'to_unit_id' => '', 'conversion_factor' => '']

    public function mount()
    {
        if (!auth()->user()->can('manage product units')) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        $products = Product::query()
            ->with(['unit', 'category'])
            ->withCount('unitConversions') // Assuming relationship exists or we add it
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            })
            ->when($this->category_id, function($q) {
                $q->where('category_id', $this->category_id);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.master.product-unit', [
            'products' => $products,
            'units' => Unit::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
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
                'to_unit_id' => $c->to_unit_id,
                'conversion_factor' => (float)$c->conversion_factor,
                'input_factor' => (float)$c->conversion_factor, // This will be adjusted in UI
            ];
        })->toArray();

        // Adjust input_factor for hierarchical display
        foreach ($this->conversions as &$conv) {
            if ($conv['to_unit_id'] != $this->base_unit_id) {
                $target = $this->editingProduct->unitConversions->where('from_unit_id', $conv['to_unit_id'])->first();
                if ($target) {
                    $conv['input_factor'] = $conv['conversion_factor'] / $target->conversion_factor;
                }
            }
        }

        $this->showModal = true;
    }

    public function addConversion()
    {
        $this->conversions[] = [
            'id' => null,
            'from_unit_id' => '',
            'to_unit_id' => $this->base_unit_id,
            'input_factor' => '',
            'conversion_factor' => '', // Calculated on save
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
            'conversions.*.to_unit_id' => 'required|exists:units,id',
            'conversions.*.input_factor' => 'required|numeric|min:0.0001',
        ]);

        // Validate circular dependencies and missing parents
        $fromUnits = array_column($this->conversions, 'from_unit_id');
        foreach ($this->conversions as $conv) {
            if ($conv['to_unit_id'] != $this->base_unit_id && !in_array($conv['to_unit_id'], $fromUnits)) {
                $this->addError('conversions', 'Satuan target harus berupa satuan dasar atau satuan yang sudah dikonversi.');
                return;
            }
            if ($conv['from_unit_id'] == $conv['to_unit_id']) {
                $this->addError('conversions', 'Satuan asal dan target tidak boleh sama.');
                return;
            }
        }

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

        // Resolve factors iteratively (hierarchical resolution)
        $resolvedConversions = [];
        $remaining = $this->conversions;
        
        // Base case: Direct to base
        foreach ($remaining as $key => $conv) {
            if ($conv['to_unit_id'] == $this->base_unit_id) {
                $conv['conversion_factor'] = $conv['input_factor'];
                $resolvedConversions[$conv['from_unit_id']] = $conv;
                unset($remaining[$key]);
            }
        }

        // Recursive case: Multi-level (max 5 levels to prevent infinite loops)
        for ($i = 0; $i < 5; $i++) {
            if (empty($remaining)) break;
            foreach ($remaining as $key => $conv) {
                if (isset($resolvedConversions[$conv['to_unit_id']])) {
                    $conv['conversion_factor'] = $conv['input_factor'] * $resolvedConversions[$conv['to_unit_id']]['conversion_factor'];
                    $resolvedConversions[$conv['from_unit_id']] = $conv;
                    unset($remaining[$key]);
                }
            }
        }

        if (!empty($remaining)) {
            $this->addError('conversions', 'Gagal menyelesaikan hierarki satuan. Pastikan tidak ada dependensi melingkar.');
            return;
        }

        foreach ($resolvedConversions as $conv) {
            UnitConversion::updateOrCreate(
                [
                    'id' => $conv['id'],
                    'product_id' => $this->editingProduct->id,
                ],
                [
                    'from_unit_id' => $conv['from_unit_id'],
                    'to_unit_id' => $conv['to_unit_id'],
                    'conversion_factor' => $conv['conversion_factor'],
                ]
            );
        }

        $this->showModal = false;

        ActivityLog::log([
            'action' => 'updated',
            'module' => 'products',
            'description' => "Memperbarui pengaturan satuan/konversi untuk obat: {$this->editingProduct->name}",
            'new_values' => [
                'base_unit_id' => $this->base_unit_id,
                'conversions' => $this->conversions
            ]
        ]);

        $this->dispatch('notify', 'Pengaturan satuan berhasil disimpan.');
    }

    public function delete($productId)
    {
        $product = Product::findOrFail($productId);
        // Delete all conversions
        UnitConversion::where('product_id', $productId)->delete();
        
        ActivityLog::log([
            'action' => 'updated',
            'module' => 'products',
            'description' => "Mereset pengaturan konversi satuan untuk obat: {$product->name}",
        ]);

        $this->dispatch('notify', 'Pengaturan konversi satuan berhasil direset.');
    }
}
