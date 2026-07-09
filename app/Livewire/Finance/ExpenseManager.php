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
    public $perPage = 10;
    public $description;
    public $amount;
    public $category;
    public $type = 'expense';
    public $accountId;
    
    public $showModal = false;
    public $isEditing = false;
    public $editId;

    public $search = '';

    // Period filter
    public $filterPeriod = 'this_month';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    public $sortBy = 'date';
    public $sortDirection = 'desc';

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function mount()
    {
        // Simple permission check
        if (!auth()->user()->can('view expenses')) {
             abort(403, 'Unauthorized');
        }
        
        $this->date = Carbon::now()->format('Y-m-d');
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedFilterPeriod()
    {
        if ($this->filterPeriod !== 'custom') {
            $this->filterDateFrom = '';
            $this->filterDateTo = '';
        }
        $this->resetPage();
    }

    public function updatedFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo()
    {
        $this->resetPage();
    }

    private function getDateRange()
    {
        return match ($this->filterPeriod) {
            'today' => [
                Carbon::today()->format('Y-m-d'),
                Carbon::today()->format('Y-m-d'),
            ],
            'this_week' => [
                Carbon::now()->startOfWeek()->format('Y-m-d'),
                Carbon::now()->endOfWeek()->format('Y-m-d'),
            ],
            'this_month' => [
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->endOfMonth()->format('Y-m-d'),
            ],
            'custom' => [
                $this->filterDateFrom ?: null,
                $this->filterDateTo ?: null,
            ],
            default => [null, null], // 'all'
        };
    }

    private function applyDateFilter($query)
    {
        [$from, $to] = $this->getDateRange();

        if ($from && $to) {
            $query->whereBetween('date', [$from, $to]);
        } elseif ($from) {
            $query->where('date', '>=', $from);
        } elseif ($to) {
            $query->where('date', '<=', $to);
        }

        return $query;
    }

    public function create()
    {
        $this->reset(['description', 'amount', 'category', 'accountId', 'isEditing', 'editId']);
        $this->date = Carbon::now()->format('Y-m-d');
        $this->type = 'expense';

        // Set default category dynamically
        $defaultCat = ExpenseCategory::active()->orderBy('name')->first();
        $this->category = $defaultCat ? $defaultCat->name : null;

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
        $this->type = $expense->type;
        $this->accountId = $expense->account_id;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'date' => ['required', 'date', 'before_or_equal:' . date('Y-m-d')],
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'type' => 'required|in:expense,income',
            'accountId' => 'nullable|exists:accounts,id',
        ]);

        // Recording the expense/income itself must never fail just because the optional
        // auto-journal step below has trouble (e.g. a matching Beban/Pendapatan account
        // doesn't exist yet) - that used to roll back the whole save and make it look like
        // the form randomly refused to save.
        try {
            if ($this->isEditing) {
                $expense = Expense::findOrFail($this->editId);
                $oldData = $expense->toArray();
                $expense->update([
                    'date' => $this->date,
                    'description' => $this->description,
                    'amount' => $this->amount,
                    'category' => $this->category,
                    'type' => $this->type,
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
                    'type' => $this->type,
                    'account_id' => $this->accountId,
                    'user_id' => auth()->id(),
                ]);

                ActivityLog::log([
                    'action' => 'created',
                    'module' => 'expenses',
                    'description' => "Menambah pengeluaran baru: {$this->description}",
                    'new_values' => $expense->toArray()
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan pengeluaran: ' . $e->getMessage());
            return;
        }

        $journalWarning = null;
        if (!$this->isEditing && $this->accountId) {
            try {
                (new \App\Services\AccountingService())->postExpenseJournal($expense->id, $this->accountId);
            } catch (\Exception $e) {
                $journalWarning = $e->getMessage();
            }
        }

        $this->showModal = false;
        $this->reset(['description', 'amount', 'category', 'type', 'accountId', 'isEditing', 'editId']);
        $this->type = 'expense';

        if ($journalWarning) {
            session()->flash('error', 'Data pengeluaran berhasil disimpan, tapi jurnal akuntansi otomatis gagal dibuat: ' . $journalWarning);
        } else {
            session()->flash('message', 'Data pengeluaran berhasil disimpan.');
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        [$from, $to] = $this->getDateRange();
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ExpensesExport($this->search, $from, $to), 
            'Laporan-Pengeluaran-' . date('d-m-Y') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        [$from, $to] = $this->getDateRange();
        $this->dispatch('open-pdf', url: route('pdf.expenses', [
            'search' => $this->search,
            'from' => $from,
            'to' => $to,
        ]));
    }

    
    public function updatingPerPage()
    {
        $this->resetPage();
    }
public function render()
    {
        $query = Expense::with(['user', 'account'])
            ->when($this->search, function($q) {
                $q->where(function($sub) {
                    $sub->where('description', 'like', '%' . $this->search . '%')
                         ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            });

        $this->applyDateFilter($query);

        $expenses = $query->orderBy($this->sortBy, $this->sortDirection)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
        $expenses->onEachSide(1);
        
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
