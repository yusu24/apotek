<?php

namespace App\Livewire\Finance;

use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\ActivityLog;
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
    // Removed to separate component

    public function mount()
    {
        // Simple permission check
        if (!auth()->user()->can('view expenses')) {
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
            $oldData = $expense->toArray();
            $expense->update([
                'date' => $this->date,
                'description' => $this->description,
                'amount' => $this->amount,
                'category' => $this->category,
            ]);

            ActivityLog::log([
                'action' => 'updated',
                'module' => 'expenses',
                'description' => "Memperbarui pengeluaran: {$this->description}",
                'old_values' => $oldData,
                'new_values' => $expense->fresh()->toArray()
            ]);
        } else {
            $expense = Expense::create([
                'date' => $this->date,
                'description' => $this->description,
                'amount' => $this->amount,
                'category' => $this->category,
                'user_id' => auth()->id(),
            ]);

            ActivityLog::log([
                'action' => 'created',
                'module' => 'expenses',
                'description' => "Menambah pengeluaran baru: {$this->description}",
                'new_values' => $expense->toArray()
            ]);
        }

        $this->showModal = false;
        $this->reset(['description', 'amount', 'category', 'isEditing', 'editId']);
        session()->flash('message', 'Data pengeluaran berhasil disimpan.');
    }

    public function delete($id)
    {
        $expense = Expense::findOrFail($id);
        $oldData = $expense->toArray();
        $expense->delete();

        ActivityLog::log([
            'action' => 'deleted',
            'module' => 'expenses',
            'description' => "Menghapus pengeluaran: {$oldData['description']}",
            'old_values' => $oldData
        ]);

        session()->flash('message', 'Data pengeluaran dihapus.');
    }

    // Category Management
    // Removed to separate component

    public function render()
    {
        $expenses = Expense::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->onEachSide(2);
        
        $categories = ExpenseCategory::active()->orderBy('name')->get();
            
        return view('livewire.finance.expense-manager', [
            'expenses' => $expenses,
            'categories' => $categories,
        ]);
    }
}
