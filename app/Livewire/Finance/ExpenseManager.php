<?php

namespace App\Livewire\Finance;

use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;

#[Layout('layouts.app')]
class ExpenseManager extends Component
{
    use WithPagination;

    public $date;
    public $description;
    public $amount;
    public $category;
    
    public $showModal = false;
    public $isEditing = false;
    public $editId;

    // Category Management
    public $showCategoryModal = false;
    public $categoryName;
    public $categoryEditId = null;

    public function mount()
    {
        // Simple permission check
        if (!auth()->user()->can('view finance') && !auth()->user()->can('manage finance')) {
             abort(403, 'Unauthorized');
        }
        
        $this->date = Carbon::now()->format('Y-m-d');
    }

    public function create()
    {
        $this->reset(['description', 'amount', 'category', 'isEditing', 'editId']);
        $this->date = Carbon::now()->format('Y-m-d');
        $this->category = 'Operasional'; // Set default
        $this->showModal = true;
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $this->editId = $expense->id;
        $this->date = $expense->date;
        $this->description = $expense->description;
        $this->amount = $expense->amount;
        $this->category = $expense->category;
        
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        if ($this->isEditing) {
            $expense = Expense::findOrFail($this->editId);
            $expense->update([
                'date' => $this->date,
                'description' => $this->description,
                'amount' => $this->amount,
                'category' => $this->category,
            ]);
        } else {
            Expense::create([
                'date' => $this->date,
                'description' => $this->description,
                'amount' => $this->amount,
                'category' => $this->category,
                'user_id' => auth()->id(),
            ]);
        }

        $this->showModal = false;
        $this->reset(['description', 'amount', 'category', 'isEditing', 'editId']);
        session()->flash('message', 'Data pengeluaran berhasil disimpan.');
    }

    public function delete($id)
    {
        Expense::findOrFail($id)->delete();
        session()->flash('message', 'Data pengeluaran dihapus.');
    }

    // Category Management Methods
    public function openCategoryManager()
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage expense categories')) {
            abort(403, 'Unauthorized');
        }
        
        $this->reset(['categoryName', 'categoryEditId']);
        $this->showCategoryModal = true;
    }

    public function editCategory($id)
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage expense categories')) {
            abort(403, 'Unauthorized');
        }

        $category = ExpenseCategory::findOrFail($id);
        $this->categoryEditId = $category->id;
        $this->categoryName = $category->name;
    }

    public function saveCategory()
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage expense categories')) {
            abort(403, 'Unauthorized');
        }

        $this->validate([
            'categoryName' => 'required|string|max:255|unique:expense_categories,name,' . $this->categoryEditId,
        ], [
            'categoryName.required' => 'Nama kategori harus diisi.',
            'categoryName.unique' => 'Nama kategori sudah ada.',
        ]);

        if ($this->categoryEditId) {
            $category = ExpenseCategory::findOrFail($this->categoryEditId);
            $category->update(['name' => $this->categoryName]);
            session()->flash('categoryMessage', 'Kategori berhasil diperbarui.');
        } else {
            ExpenseCategory::create([
                'name' => $this->categoryName,
                'is_active' => true
            ]);
            session()->flash('categoryMessage', 'Kategori berhasil ditambahkan.');
        }

        $this->reset(['categoryName', 'categoryEditId']);
    }

    public function deleteCategory($id)
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage expense categories')) {
            abort(403, 'Unauthorized');
        }

        try {
            ExpenseCategory::findOrFail($id)->delete();
            session()->flash('categoryMessage', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('categoryError', 'Gagal menghapus kategori. Mungkin sedang digunakan.');
        }
    }

    public function cancelEditCategory()
    {
        $this->reset(['categoryName', 'categoryEditId']);
    }

    public function render()
    {
        $expenses = Expense::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->onEachSide(2);
        
        $categories = ExpenseCategory::active()->orderBy('name')->get();
        $allCategories = ExpenseCategory::orderBy('name')->get(); // For management modal
            
        return view('livewire.finance.expense-manager', [
            'expenses' => $expenses,
            'categories' => $categories,
            'allCategories' => $allCategories,
        ]);
    }
}
