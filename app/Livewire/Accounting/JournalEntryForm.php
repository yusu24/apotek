<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Livewire\Component;

class JournalEntryForm extends Component
{
    public $date;
    public $description;
    public $lines = [];
    public $accounts = [];

    // Totals for display
    public $totalDebit = 0;
    public $totalCredit = 0;
    public $difference = 0;

    public function mount()
    {
        if (!auth()->user()->can('create journal')) {
            abort(403, 'Unauthorized');
        }

        $this->date = now()->format('Y-m-d');
        $this->accounts = Account::active()->orderBy('code')->get();
        
        // Initialize with 2 empty lines
        $this->addLine();
        $this->addLine();
    }

    public function addLine()
    {
        $this->lines[] = [
            'account_id' => '',
            'debit' => 0,
            'credit' => 0,
            'notes' => '',
        ];
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        $this->calculateTotals();
    }

    public function updatedLines()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->totalDebit = 0;
        $this->totalCredit = 0;

        foreach ($this->lines as $line) {
            $this->totalDebit += (float) ($line['debit'] ?? 0);
            $this->totalCredit += (float) ($line['credit'] ?? 0);
        }

        $this->difference = $this->totalDebit - $this->totalCredit;
    }

    public function save()
    {
        if (!auth()->user()->can('create journal')) {
            abort(403, 'Unauthorized');
        }

        $this->calculateTotals();

        $this->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        if (abs($this->difference) > 0.01) {
            $this->addError('balance', 'Jurnal tidak seimbang. Total Debit harus sama dengan Total Kredit.');
            return;
        }

        // Check if total debit is not zero (empty journal)
        if ($this->totalDebit <= 0) {
            $this->addError('balance', 'Total transaksi tidak boleh nol.');
            return;
        }

        try {
            $accountingService = new AccountingService();
            $data = [
                'date' => $this->date,
                'description' => $this->description,
                'lines' => $this->lines,
                'auto_post' => true,
            ];

            $accountingService->createJournalEntry($data);

            session()->flash('message', 'Jurnal berhasil disimpan dan diposting.');
            return redirect()->route('accounting.journals.index');

        } catch (\Exception $e) {
            $this->addError('system', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.accounting.journal-entry-form')->layout('layouts.app');
    }
}
