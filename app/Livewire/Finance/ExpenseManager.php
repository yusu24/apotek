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
    public $accountId;
    
    public $showModal = false;
    public $isEditing = false;
    public $editId;

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
        $this->reset(['description', 'amount', 'category', 'accountId', 'isEditing', 'editId']);
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
        $this->accountId = $expense->account_id;
        
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
            'accountId' => 'nullable|exists:accounts,id',
        ]);

        \DB::beginTransaction();
        try {
            if ($this->isEditing) {
                $expense = Expense::findOrFail($this->editId);
                $oldData = $expense->toArray();
                $expense->update([
                    'date' => $this->date,
                    'description' => $this->description,
                    'amount' => $this->amount,
                    'category' => $this->category,
                    'account_id' => $this->accountId,
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
                    'account_id' => $this->accountId,
                    'user_id' => auth()->id(),
                ]);

                ActivityLog::log([
                    'action' => 'created',
                    'module' => 'expenses',
                    'description' => "Menambah pengeluaran baru: {$this->description}",
                    'new_values' => $expense->toArray()
                ]);

                // Create Journal Entry if account is selected
                if ($this->accountId) {
                    $accountingService = new \App\Services\AccountingService();
                    $accountingService->postExpenseJournal($expense->id, $this->accountId);
                }
            }

            \DB::commit();
            $this->showModal = false;
            $this->reset(['description', 'amount', 'category', 'accountId', 'isEditing', 'editId']);
            session()->flash('message', 'Data pengeluaran berhasil disimpan.');
        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Gagal menyimpan pengeluaran: ' . $e->getMessage());
        }
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

    public function render()
    {
        $expenses = Expense::with(['user', 'account'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->onEachSide(2);
        
        $categories = ExpenseCategory::active()->orderBy('name')->get();
        
        // Load active accounts (Kas, Bank, Utang)
        $accounts = \App\Models\Account::active()
            ->whereIn('type', ['asset', 'liability'])
            ->orderBy('code')
            ->get();
            
        return view('livewire.finance.expense-manager', [
            'expenses' => $expenses,
            'categories' => $categories,
            'accounts' => $accounts,
        ]);
    }
}
