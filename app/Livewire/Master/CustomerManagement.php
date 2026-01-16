<?php

namespace App\Livewire\Master;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CustomerManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $phone, $address;
    public $customerId;
    public $isEditMode = false;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->name = '';
        $this->phone = '';
        $this->address = '';
        $this->customerId = null;
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
        $this->resetFields();
    }

    public function store()
    {
        if (!auth()->user()->can('manage master data')) {
            abort(403);
        }

        $this->validate();

        Customer::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        session()->flash('success', 'Pelanggan berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->address = $customer->address;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        if (!auth()->user()->can('manage master data')) {
            abort(403);
        }

        $this->validate();

        $customer = Customer::findOrFail($this->customerId);
        $customer->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        session()->flash('success', 'Pelanggan berhasil diperbarui.');
        $this->closeModal();
    }

    public function delete($id)
    {
        if (!auth()->user()->can('manage master data')) {
            abort(403);
        }

        Customer::findOrFail($id)->delete();
        session()->flash('success', 'Pelanggan berhasil dihapus.');
    }

    public function render()
    {
        $customers = Customer::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.master.customer-management', [
            'customers' => $customers
        ]);
    }
    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomersExport, 'Data-Pelanggan-' . date('d-m-Y') . '.xlsx');
    }
}
