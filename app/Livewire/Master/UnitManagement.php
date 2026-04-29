<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Unit;
use App\Models\ActivityLog;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UnitManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showModal = false;
    public $editMode = false;
    public $unitId;
    public $name;

    protected $rules = [
        'name' => 'required|min:2|unique:units,name',
    ];

    public function mount()
    {
        if (!auth()->user()->can('manage units') && !auth()->user()->can('manage master data')) {
            // Fallback for backward compatibility or simple check
             abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        /** @var \Illuminate\Pagination\LengthAwarePaginator $units */
        $units = Unit::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate($this->perPage);
        $units->onEachSide(1);

        return view('livewire.master.unit-management', [
            'units' => $units
        ]);
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
        if (!auth()->user()->can('manage units')) {
            abort(403, 'Unauthorized');
        }

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
        if (!auth()->user()->can('manage units')) {
            abort(403, 'Unauthorized');
        }

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
