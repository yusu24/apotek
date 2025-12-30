<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use App\Models\ExpenseCategory;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ExpenseCategoryIndex extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $search = '';
    public $categoryId;
    public $name;
    public $description;
    public $isEditMode = false;

    public function mount()
    {
        if (!auth()->user()->can('manage expense categories')) {
            abort(403, 'Unauthorized');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = ExpenseCategory::when($this->search, function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.finance.expense-category-index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $this->reset(['name', 'description', 'categoryId', 'isEditMode']);
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $this->categoryId,
        ], [
            'name.required' => 'Nama kategori harus diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
        ]);

        if ($this->isEditMode) {
            $category = ExpenseCategory::findOrFail($this->categoryId);
            $oldData = $category->toArray();
            $category->update([
                'name' => $this->name,
                'description' => $this->description
            ]);
            
            ActivityLog::log([
                'action' => 'updated',
                'module' => 'expenses',
                'description' => "Memperbarui kategori pengeluaran: {$this->name}",
                'old_values' => $oldData,
                'new_values' => $category->fresh()->toArray()
            ]);

            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            $category = ExpenseCategory::create([
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => true
            ]);

            ActivityLog::log([
                'action' => 'created',
                'module' => 'expenses',
                'description' => "Menambah kategori pengeluaran baru: {$this->name}",
                'new_values' => $category->toArray()
            ]);

            session()->flash('message', 'Kategori berhasil ditambahkan.');
        }

        $this->isOpen = false;
        $this->reset(['name', 'description', 'categoryId', 'isEditMode']);
    }

    public function delete($id)
    {
        try {
            $category = ExpenseCategory::findOrFail($id);
            $oldData = $category->toArray();
            $category->delete();

            ActivityLog::log([
                'action' => 'deleted',
                'module' => 'expenses',
                'description' => "Menghapus kategori pengeluaran: {$oldData['name']}",
                'old_values' => $oldData
            ]);

            session()->flash('message', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus kategori. Mungkin sedang digunakan dalam transaksi.');
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['name', 'description', 'categoryId', 'isEditMode']);
    }
}
