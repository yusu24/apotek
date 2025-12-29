<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class AccountIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    
    // Modal State
    public $showModal = false;
    public $isEditMode = false;
    public $accountId;
    
    // Form Fields
    public $code;
    public $name;
    public $type = 'asset';
    public $category = 'current_asset';
    public $description;
    public $is_active = true;

    protected $rules = [
        'code' => 'required|string|unique:accounts,code',
        'name' => 'required|string|max:255',
        'type' => 'required|in:asset,liability,equity,revenue,expense',
        'category' => 'required|string',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        if (!auth()->user()->can('view accounts')) {
            abort(403, 'Unauthorized');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $account = Account::findOrFail($id);
        $this->accountId = $account->id;
        $this->code = $account->code;
        $this->name = $account->name;
        $this->type = $account->type;
        $this->category = $account->category;
        $this->is_active = $account->is_active;
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function store()
    {
        if (!auth()->user()->can('manage accounts')) {
            abort(403, 'Unauthorized');
        }

        $rules = $this->rules;
        if ($this->isEditMode) {
            $rules['code'] = 'required|string|unique:accounts,code,' . $this->accountId;
        }

        $this->validate($rules);

        if ($this->isEditMode) {
            $account = Account::findOrFail($this->accountId);
            $account->update([
                'code' => $this->code,
                'name' => $this->name,
                'type' => $this->type,
                'category' => $this->category,
                'is_active' => $this->is_active,
            ]);
            
            session()->flash('message', 'Akun berhasil diperbarui.');
        } else {
            Account::create([
                'code' => $this->code,
                'name' => $this->name,
                'type' => $this->type,
                'category' => $this->category,
                'is_active' => $this->is_active,
                'is_system' => false,
                'balance' => 0,
            ]);
            
            session()->flash('message', 'Akun berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        if (!auth()->user()->can('manage accounts')) {
            abort(403, 'Unauthorized');
        }

        $account = Account::findOrFail($id);

        if (!$account->canDelete()) {
            session()->flash('error', 'Akun sistem atau akun yang memiliki transaksi tidak dapat dihapus.');
            return;
        }

        $account->delete();
        session()->flash('message', 'Akun berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->code = '';
        $this->name = '';
        $this->type = 'asset';
        $this->category = 'current_asset';
        $this->is_active = true;
        $this->accountId = null;
    }

    public function render()
    {
        $query = Account::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        // Order by Code ascending
        $accounts = $query->orderBy('code')->paginate(10);

        return view('livewire.accounting.account-index', [
            'accounts' => $accounts
        ])->layout('layouts.app');
    }
}
