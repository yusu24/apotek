<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
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
        'contact_person' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
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
        $this->contact_person = '';
        $this->phone = '';
        $this->address = '';
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

        Supplier::create([
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        session()->flash('message', 'Pemasok berhasil ditambahkan.');
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
            $supplier->update([
                'name' => $this->name,
                'contact_person' => $this->contact_person,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);

            session()->flash('message', 'Pemasok berhasil diperbarui.');
            $this->closeModal();
            $this->resetFields();
        }
    }

    public function delete($id)
    {
        Supplier::findOrFail($id)->delete();
        session()->flash('message', 'Pemasok berhasil dihapus.');
    }
}
