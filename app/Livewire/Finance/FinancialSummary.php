<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FinancialSummary extends Component
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        if (!auth()->user()->can('view reports')) {
            abort(403, 'Unauthorized');
        }

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        // Get Cash & Bank Accounts
        $cashAndBank = Account::active()
            ->where('category', 'current_asset')
            ->where(function($q) {
                $q->where('name', 'like', '%Kas%')
                  ->orWhere('name', 'like', '%Bank%');
            })
            ->orderBy('code')
            ->get();

        // Get Debt Accounts
        $debts = Account::active()
            ->where('type', 'liability')
            ->orderBy('code')
            ->get();

        // Recent Transactions for these accounts
        $recentTransactions = JournalEntryLine::with(['journalEntry', 'account'])
            ->whereIn('account_id', $cashAndBank->pluck('id')->merge($debts->pluck('id')))
            ->whereHas('journalEntry', function($q) {
                $q->whereDate('date', '>=', $this->startDate)
                  ->whereDate('date', '<=', $this->endDate);
            })
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.date', 'desc')
            ->orderBy('journal_entries.created_at', 'desc')
            ->select('journal_entry_lines.*')
            ->take(15)
            ->get();

        // Current Net Position
        $netPosition = $cashAndBank->sum('balance') - $debts->sum('balance');

        // Add Saldo (Balance) to each transaction row by calculating backwards
        $runningBalance = $netPosition;
        foreach ($recentTransactions as $line) {
            $line->running_balance = $runningBalance;
            // Reverse the impact of this line to get the balance BEFORE it
            // Net Position Impact = Debit - Credit
            $impact = $line->debit - $line->credit;
            $runningBalance -= $impact;
        }

        return view('livewire.finance.financial-summary', [
            'cashAndBank' => $cashAndBank,
            'debts' => $debts,
            'recentTransactions' => $recentTransactions,
            'totalCash' => $cashAndBank->sum('balance'),
            'totalDebt' => $debts->sum('balance'),
            'netPosition' => $netPosition,
        ]);
    }
}
