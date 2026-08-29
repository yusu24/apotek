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
    public $filterType = ''; // '' = Semua, 'expense' = Pengeluaran, 'income' = Pemasukan
    public $perPage = 10;
    public $categoryId;
    public $name;
    public $type = 'expense';
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

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        /** @var \Illuminate\Pagination\LengthAwarePaginator $categories */
        $categories = ExpenseCategory::when($this->search, function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterType, function($q) {
                $q->where('type', $this->filterType);
            })
            ->orderBy('type')
            ->orderBy('name')
            ->paginate($this->perPage);
        $categories->onEachSide(1);

        $totalExpenseCategories = ExpenseCategory::where('type', 'expense')->count();
        $totalIncomeCategories  = ExpenseCategory::where('type', 'income')->count();

        return view('livewire.finance.expense-category-index', [
            'categories'             => $categories,
            'totalExpenseCategories' => $totalExpenseCategories,
            'totalIncomeCategories'  => $totalIncomeCategories,
        ]);
    }

    public function create($type = 'expense')
    {
        $this->reset(['name', 'description', 'categoryId', 'isEditMode']);
        $this->type = in_array($type, ['expense', 'income']) ? $type : ($this->filterType ?: 'expense');
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->type = $category->type ?? 'expense';
        $this->description = $category->description ?? '';
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $this->categoryId,
            'type' => 'required|in:expense,income',
        ], [
            'name.required' => 'Nama kategori harus diisi.',
            'name.unique'   => 'Nama kategori sudah ada.',
            'type.required' => 'Tipe kategori harus dipilih.',
        ]);

        $typeLabel = $this->type === 'income' ? 'Pemasukan' : 'Pengeluaran';

        if ($this->isEditMode) {
            $category = ExpenseCategory::findOrFail($this->categoryId);
            $oldData = $category->toArray();
            $category->update([
                'name'        => $this->name,
                'type'        => $this->type,
                'description' => $this->description
            ]);
            
            ActivityLog::log([
                'action'      => 'updated',
                'module'      => 'expenses',
                'description' => "Memperbarui kategori {$typeLabel}: {$this->name}",
                'old_values'  => $oldData,
                'new_values'  => $category->fresh()->toArray()
            ]);

            session()->flash('message', "Kategori {$typeLabel} berhasil diperbarui.");
        } else {
            $category = ExpenseCategory::create([
                'name'        => $this->name,
                'type'        => $this->type,
                'description' => $this->description,
                'is_active'   => true
            ]);

            ActivityLog::log([
                'action'      => 'created',
                'module'      => 'expenses',
                'description' => "Menambah kategori {$typeLabel} baru: {$this->name}",
                'new_values'  => $category->toArray()
            ]);

            session()->flash('message', "Kategori {$typeLabel} berhasil ditambahkan.");
        }

        $this->isOpen = false;
        $this->reset(['name', 'description', 'categoryId', 'isEditMode', 'type']);
    }

    public function delete($id)
    {
        try {
            $category = ExpenseCategory::findOrFail($id);
            $oldData = $category->toArray();
            $typeLabel = $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
            $category->delete();

            ActivityLog::log([
                'action'      => 'deleted',
                'module'      => 'expenses',
                'description' => "Menghapus kategori {$typeLabel}: {$oldData['name']}",
                'old_values'  => $oldData
            ]);

            session()->flash('message', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus kategori. Mungkin sedang digunakan dalam transaksi.');
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['name', 'description', 'categoryId', 'isEditMode', 'type']);
    }
}
