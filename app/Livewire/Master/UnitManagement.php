<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Unit;
use App\Models\ActivityLog;

class UnitManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $unitId;
    public $name;

    protected $rules = [
        'name' => 'required|min:2|unique:units,name',
    ];

    public function render()
    {
        $units = Unit::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.master.unit-management', [
            'units' => $units
        ])->layout('layouts.app');
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
        $this->name = '';
        $this->unitId = null;
        $this->editMode = false;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2|unique:units,name,' . ($this->unitId ?? 'NULL'),
        ]);

        if ($this->editMode) {
            $unit = Unit::findOrFail($this->unitId);
            $unit->update(['name' => $this->name]);
            
            ActivityLog::log([
                'action' => 'updated',
                'module' => 'master',
                'description' => "Memperbarui nama satuan menjadi: {$this->name}",
            ]);

            $this->dispatch('notify', 'Satuan berhasil diperbarui.');
        } else {
            Unit::create(['name' => $this->name]);

            ActivityLog::log([
                'action' => 'created',
                'module' => 'master',
                'description' => "Menambah satuan baru: {$this->name}",
            ]);

            $this->dispatch('notify', 'Satuan berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $this->unitId = $id;
        $this->name = $unit->name;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $unit = Unit::findOrFail($id);
        
        // Check if used by products
        $usedCount = \App\Models\Product::where('unit_id', $id)->count();
        if ($usedCount > 0) {
            $this->dispatch('notify', ['message' => 'Satuan tidak bisa dihapus karena sedang digunakan oleh ' . $usedCount . ' produk.', 'type' => 'error']);
            return;
        }

        $unitName = $unit->name;
        $unit->delete();

        ActivityLog::log([
            'action' => 'deleted',
            'module' => 'master',
            'description' => "Menghapus satuan: {$unitName}",
        ]);

        $this->dispatch('notify', 'Satuan berhasil dihapus.');
    }
}
