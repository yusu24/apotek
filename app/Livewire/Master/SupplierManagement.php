<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use App\Models\Supplier;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SupplierManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $contact_person, $phone, $address;
    public $supplierId;
    public $isEditMode = false;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'contact_person' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string',
    ];

    public function mount()
    {
        if (!auth()->user()->can('manage suppliers')) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        $suppliers = Supplier::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('contact_person', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.master.supplier-management', [
            'suppliers' => $suppliers,
        ]);
    }

    public function resetFields()
    {
        $this->name = '';
        $this->contact_person = '-';
        $this->phone = '-';
        $this->address = '-';
        $this->supplierId = null;
        $this->isEditMode = false;
    }

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function store()
    {
        $this->validate();

        $supplier = Supplier::create([
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        ActivityLog::log([
            'action' => 'created',
            'module' => 'suppliers',
            'description' => "Menambah supplier baru: {$this->name}",
            'new_values' => $supplier->toArray()
        ]);

        session()->flash('message', 'Supplier berhasil ditambahkan.');
        $this->closeModal();
        $this->resetFields();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $id;
        $this->name = $supplier->name;
        $this->contact_person = $supplier->contact_person;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        if ($this->supplierId) {
            $supplier = Supplier::findOrFail($this->supplierId);
            $oldData = $supplier->toArray();
            $supplier->update([
                'name' => $this->name,
                'contact_person' => $this->contact_person,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);

            ActivityLog::log([
                'action' => 'updated',
                'module' => 'suppliers',
                'description' => "Memperbarui supplier: {$this->name}",
                'old_values' => $oldData,
                'new_values' => $supplier->fresh()->toArray()
            ]);

            session()->flash('message', 'Supplier berhasil diperbarui.');
            $this->closeModal();
            $this->resetFields();
        }
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $oldData = $supplier->toArray();
        $supplier->delete();

        ActivityLog::log([
            'action' => 'deleted',
            'module' => 'suppliers',
            'description' => "Menghapus supplier: {$oldData['name']}",
            'old_values' => $oldData
        ]);

        session()->flash('message', 'Supplier berhasil dihapus.');
    }
}
