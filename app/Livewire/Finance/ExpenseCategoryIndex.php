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

    public $showModal = false;
    public $categoryId;
    public $name;
    public $isEditMode = false;

    public function mount()
    {
        if (!auth()->user()->can('manage expense categories')) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        $categories = ExpenseCategory::orderBy('name')
            ->paginate(10);

        return view('livewire.finance.expense-category-index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $this->reset(['name', 'categoryId', 'isEditMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->isEditMode = true;
        $this->showModal = true;
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
            $category->update(['name' => $this->name]);
            
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

        $this->showModal = false;
        $this->reset(['name', 'categoryId', 'isEditMode']);
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
        $this->showModal = false;
        $this->reset(['name', 'categoryId', 'isEditMode']);
    }
}
