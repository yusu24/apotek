<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\UnitConversion;
use App\Models\Product;
use App\Models\Unit;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UnitConversionManagement extends Component
{
    public $conversions;
    public $products;
    public $units;
    
    // Form properties
    public $showModal = false;
    public $editMode = false;
    public $conversionId = null;
    public $product_id = '';
    public $from_unit_id = '';
    public $to_unit_id = '';
    public $conversion_factor = '';

    public function mount()
    {
        if (!auth()->user()->can('manage master data')) {
            abort(403, 'Unauthorized');
        }
        
        $this->loadData();
    }

    public function loadData()
    {
        $this->conversions = UnitConversion::with(['product', 'fromUnit', 'toUnit'])->get();
        $this->products = Product::select('id', 'name')->orderBy('name')->get();
        $this->units = Unit::all();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editMode = false;
        $this->conversionId = null;
        $this->product_id = '';
        $this->from_unit_id = '';
        $this->to_unit_id = '';
        $this->conversion_factor = '';
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'from_unit_id' => 'required|exists:units,id',
            'to_unit_id' => 'required|exists:units,id|different:from_unit_id',
            'conversion_factor' => 'required|numeric|min:0.0001',
        ], [
            'product_id.required' => 'Produk wajib dipilih',
            'from_unit_id.required' => 'Satuan asal wajib dipilih',
            'to_unit_id.required' => 'Satuan tujuan wajib dipilih',
            'to_unit_id.different' => 'Satuan tujuan harus berbeda dengan satuan asal',
            'conversion_factor.required' => 'Faktor konversi wajib diisi',
            'conversion_factor.min' => 'Faktor konversi harus lebih dari 0',
        ]);

        if ($this->editMode) {
            $conversion = UnitConversion::find($this->conversionId);
            $conversion->update([
                'product_id' => $this->product_id,
                'from_unit_id' => $this->from_unit_id,
                'to_unit_id' => $this->to_unit_id,
                'conversion_factor' => $this->conversion_factor,
            ]);
            session()->flash('message', 'Konversi satuan berhasil diperbarui.');
        } else {
            UnitConversion::create([
                'product_id' => $this->product_id,
                'from_unit_id' => $this->from_unit_id,
                'to_unit_id' => $this->to_unit_id,
                'conversion_factor' => $this->conversion_factor,
            ]);
            session()->flash('message', 'Konversi satuan berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->loadData();
    }

    public function edit($id)
    {
        $conversion = UnitConversion::findOrFail($id);
        $this->editMode = true;
        $this->conversionId = $id;
        $this->product_id = $conversion->product_id;
        $this->from_unit_id = $conversion->from_unit_id;
        $this->to_unit_id = $conversion->to_unit_id;
        $this->conversion_factor = $conversion->conversion_factor;
        $this->showModal = true;
    }

    public function delete($id)
    {
        UnitConversion::findOrFail($id)->delete();
        session()->flash('message', 'Konversi satuan berhasil dihapus.');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.master.unit-conversion-management');
    }
}
