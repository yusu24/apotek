<?php

namespace App\Livewire\Finance;

use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Expense;
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

    public function mount()
    {
        // Simple permission check (can be refined via roles later, assuming admin/super-admin access for now)
        if (!auth()->user()->can('view finance') && !auth()->user()->can('manage finance')) {
             abort(403, 'Unauthorized');
        }
        
        $this->date = Carbon::now()->format('Y-m-d');
    }

    public function create()
    {
        $this->reset(['description', 'amount', 'category', 'isEditing', 'editId']);
        $this->date = Carbon::now()->format('Y-m-d');
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
            'category' => 'nullable|string|max:255',
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

    public function render()
    {
        $expenses = Expense::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->onEachSide(2);
            
        return view('livewire.finance.expense-manager', [
            'expenses' => $expenses
        ]);
    }
}
