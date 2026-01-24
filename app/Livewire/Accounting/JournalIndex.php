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

    // Modal for viewing source transaction
    public $showSourceModal = false;
    public $selectedJournal = null;
    public $sourceData = null;

    // Delete confirmation
    public $showDeleteModal = false;
    public $journalToDelete = null;



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

    public function viewSource($journalId)
    {
        $this->selectedJournal = JournalEntry::find($journalId);
        
        if ($this->selectedJournal && $this->selectedJournal->hasViewableSource()) {
            $this->sourceData = $this->selectedJournal->getSourceTransaction();
            $this->showSourceModal = true;
        }
    }

    public function closeSourceModal()
    {
        $this->showSourceModal = false;
        $this->selectedJournal = null;
        $this->sourceData = null;
    }

    public function confirmDelete($journalId)
    {
        if (!auth()->user()->can('delete journals')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus jurnal.');
            return;
        }

        $journal = JournalEntry::find($journalId);
        if (!$journal) {
            session()->flash('error', 'Jurnal tidak ditemukan.');
            return;
        }

        $this->journalToDelete = $journal;
        $this->showDeleteModal = true;
    }

    public function deleteJournal()
    {
        if (!auth()->user()->can('delete journals')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus jurnal.');
            $this->showDeleteModal = false;
            return;
        }

        if (!$this->journalToDelete) {
            session()->flash('error', 'Jurnal tidak ditemukan.');
            $this->showDeleteModal = false;
            return;
        }

        try {
            $entryNumber = $this->journalToDelete->entry_number;
            
            // If journal is posted, neutralize balances first
            if ($this->journalToDelete->is_posted) {
                $this->journalToDelete->reverse();
            }

            // Delete lines first
            $this->journalToDelete->lines()->delete();
            
            // Delete journal
            $this->journalToDelete->delete();

            \App\Models\ActivityLog::log([
                'action' => 'deleted',
                'module' => 'journals',
                'description' => "Menghapus jurnal draft: {$entryNumber}",
            ]);

            session()->flash('message', 'Jurnal draft berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus jurnal: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->journalToDelete = null;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->journalToDelete = null;
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
