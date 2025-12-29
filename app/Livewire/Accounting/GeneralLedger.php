<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Livewire\Component;

class GeneralLedger extends Component
{
    public $accountId = '';
    public $startDate;
    public $endDate;
    
    // Calculated values
    public $openingBalance = 0;
    public $ledgerLines = [];
    public $endingBalance = 0;

    public function mount()
    {
        if (!auth()->user()->can('view general ledger')) {
            abort(403, 'Unauthorized');
        }

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function generateLedger()
    {
        if (!$this->accountId) {
            return;
        }

        $account = Account::findOrFail($this->accountId);
        
        // Calculate Opening Balance (Sum of all transactions before start date)
        // Adjust for Asset/Expense vs Liability/Equity/Revenue
        $increaseOnDebit = in_array($account->type, ['asset', 'expense']);

        $preLines = JournalEntryLine::whereHas('journalEntry', function($q) {
                $q->whereDate('date', '<', $this->startDate);
            })
            ->where('account_id', $this->accountId)
            ->get();

        $this->openingBalance = 0;
        foreach ($preLines as $line) {
            if ($increaseOnDebit) {
                $this->openingBalance += ($line->debit - $line->credit);
            } else {
                $this->openingBalance += ($line->credit - $line->debit);
            }
        }

        // Get Lines within Period
        $this->ledgerLines = JournalEntryLine::with('journalEntry')
            ->whereHas('journalEntry', function($q) {
                $q->whereDate('date', '>=', $this->startDate)
                  ->whereDate('date', '<=', $this->endDate);
            })
            ->where('account_id', $this->accountId)
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.date')
            ->orderBy('journal_entries.time', 'asc') // Assuming time exists or just use ID
            ->orderBy('journal_entries.id')
            ->select('journal_entry_lines.*')
            ->get();
        
        // Calculate Running Balance
        $runningBalance = $this->openingBalance;
        foreach ($this->ledgerLines as $line) {
            if ($increaseOnDebit) {
                $runningBalance += ($line->debit - $line->credit);
            } else {
                $runningBalance += ($line->credit - $line->debit);
            }
            $line->running_balance = $runningBalance;
        }

        $this->endingBalance = $runningBalance;
    }

    public function updatedAccountId()
    {
        $this->generateLedger();
    }
    
    public function updatedStartDate()
    {
        $this->generateLedger();
    }

    public function updatedEndDate()
    {
        $this->generateLedger();
    }

    public function render()
    {
        $accounts = Account::active()->orderBy('code')->get();

        return view('livewire.accounting.general-ledger', [
            'accounts' => $accounts
        ])->layout('layouts.app');
    }
}
