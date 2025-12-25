<?php

namespace App\Livewire\Settings;

use App\Models\AccountingCategory;
use Livewire\Component;
use Livewire\WithPagination;

class AccountingCategoryManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $categoryId;

    // Form fields
    public $name;
    public $code;
    public $type = 'expense';
    public $description;
    public $is_active = true;

    protected $queryString = ['search'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50',
        'type' => 'required|in:income,expense',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->code = '';
        $this->type = 'expense';
        $this->description = '';
        $this->is_active = true;
        $this->categoryId = null;
        $this->resetValidation();
    }

    public function save()
    {
        $rules = $this->rules;
        
        // If editing, allow same name/code for current record
        if ($this->editMode && $this->categoryId) {
            $rules['name'] = 'required|string|max:255|unique:accounting_categories,name,' . $this->categoryId;
            $rules['code'] = 'required|string|max:50|unique:accounting_categories,code,' . $this->categoryId;
        } else {
            $rules['name'] = 'required|string|max:255|unique:accounting_categories,name';
            $rules['code'] = 'required|string|max:50|unique:accounting_categories,code';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'type' => $this->type,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            AccountingCategory::find($this->categoryId)->update($data);
            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            AccountingCategory::create($data);
            session()->flash('message', 'Kategori berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $category = AccountingCategory::findOrFail($id);
        
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->code = $category->code;
        $this->type = $category->type;
        $this->description = $category->description;
        $this->is_active = $category->is_active;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        AccountingCategory::findOrFail($id)->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $category = AccountingCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        session()->flash('message', 'Status kategori berhasil diubah.');
    }

    public function render()
    {
        $categories = AccountingCategory::query()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.settings.accounting-category-management', [
            'categories' => $categories,
        ]);
    }
}
