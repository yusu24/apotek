<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JournalEntry;

class JournalIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sourceFilter = '';
    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        if (!auth()->user()->can('view journals')) {
            abort(403, 'Unauthorized');
        }

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->sourceFilter = '';
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $journals = JournalEntry::with(['lines.account', 'user'])
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->when($this->sourceFilter, fn($q) => $q->where('source', $this->sourceFilter))
            ->when($this->search, fn($q) => $q->where(function($query) {
                $query->where('entry_number', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            }))
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('livewire.accounting.journal-index', [
            'journals' => $journals,
        ])->layout('layouts.app');
    }
}
