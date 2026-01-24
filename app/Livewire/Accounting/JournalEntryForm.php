<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class JournalEntryForm extends Component
{
    public $date;
    public $description;
    public $lines = [];
    public $accounts = [];
    
    // For editing
    public $journalId = null;
    public $isEditing = false;

    // Totals for display
    public $totalDebit = 0;
    public $totalCredit = 0;
    public $difference = 0;

    public function mount($id = null)
    {
        if ($id) {
            if (!auth()->user()->can('edit journals')) {
                abort(403, 'Unauthorized');
            }
        } elseif (!auth()->user()->can('create journal')) {
            abort(403, 'Unauthorized');
        }

        $this->accounts = Account::active()->orderBy('code')->get();
        
        // Editing mode
        if ($id) {
            $journal = JournalEntry::with('lines')->find($id);
            
            if (!$journal) {
                session()->flash('error', 'Jurnal tidak ditemukan.');
                return redirect()->route('accounting.journals.index');
            }

            // Validate: only manual journals can be edited
            if ($journal->source !== 'manual') {
                session()->flash('error', 'Hanya jurnal manual yang dapat diedit.');
                return redirect()->route('accounting.journals.index');
            }

            $this->journalId = $journal->id;
            $this->isEditing = true;
            $this->date = $journal->date->format('Y-m-d');
            $this->description = $journal->description;
            
            // Load existing lines
            foreach ($journal->lines as $line) {
                $this->lines[] = [
                    'account_id' => $line->account_id,
                    'debit' => $line->debit > 0 ? $line->debit : null,
                    'credit' => $line->credit > 0 ? $line->credit : null,
                    'notes' => $line->notes,
                ];
            }
            
            $this->calculateTotals();
        } else {
            // Create mode
            $this->date = now()->format('Y-m-d');
            
            // Initialize with 2 empty lines
            $this->addLine();
            $this->addLine();
        }
    }

    public function addLine()
    {
        $this->lines[] = [
            'account_id' => '',
            'debit' => null,
            'credit' => null,
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

    public function getLineWarnings()
    {
        $warnings = [];
        
        foreach ($this->lines as $index => $line) {
            if (empty($line['account_id'])) {
                $warnings[$index] = null;
                continue;
            }

            $account = $this->accounts->find($line['account_id']);
            if (!$account) {
                $warnings[$index] = null;
                continue;
            }

            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            // Check if entry goes against normal balance
            if ($account->normal_balance === 'debit' && $credit > $debit) {
                $warnings[$index] = [
                    'message' => "Akun ini normalnya DEBIT",
                    'type' => 'warning'
                ];
            } elseif ($account->normal_balance === 'credit' && $debit > $credit) {
                $warnings[$index] = [
                    'message' => "Akun ini normalnya CREDIT",
                    'type' => 'warning'
                ];
            } else {
                $warnings[$index] = null;
            }
        }

        return $warnings;
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
        if ($this->isEditing) {
            if (!auth()->user()->can('edit journals')) {
                abort(403, 'Unauthorized');
            }
        } elseif (!auth()->user()->can('create journal')) {
            abort(403, 'Unauthorized');
        }

        $this->calculateTotals();

        $this->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
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
            DB::beginTransaction();

            if ($this->isEditing) {
                // Update existing journal
                $journal = JournalEntry::find($this->journalId);
                
                if (!$journal || $journal->source !== 'manual') {
                    throw new \Exception('Jurnal tidak dapat diedit atau bukan jurnal manual.');
                }

                $wasPosted = $journal->is_posted;

                // If journal was posted, neutralize balances first
                if ($wasPosted) {
                    $journal->reverse();
                }

                $journal->update([
                    'date' => $this->date,
                    'description' => $this->description,
                ]);

                // Delete old lines
                $journal->lines()->delete();

                // Create new lines
                foreach ($this->lines as $line) {
                    if ($line['account_id'] && ((float) ($line['debit'] ?? 0) > 0 || (float) ($line['credit'] ?? 0) > 0)) {
                        \App\Models\JournalEntryLine::create([
                            'journal_entry_id' => $journal->id,
                            'account_id' => $line['account_id'],
                            'debit' => (float) ($line['debit'] ?? 0),
                            'credit' => (float) ($line['credit'] ?? 0),
                            'notes' => $line['notes'] ?? null,
                        ]);
                    }
                }

                \App\Models\ActivityLog::log([
                    'action' => 'updated',
                    'module' => 'journals',
                    'description' => "Mengupdate jurnal manual: {$journal->entry_number}",
                ]);

                // Re-post if it was previously posted
                if ($wasPosted) {
                    // CRITICAL: Refresh the lines relationship cache before posting
                    $journal->load('lines');
                    $journal->post();
                }

                DB::commit();
                session()->flash('message', 'Jurnal berhasil diupdate.');
            } else {
                // Create new journal
                $accountingService = new AccountingService();
                $data = [
                    'date' => $this->date,
                    'description' => $this->description,
                    'lines' => $this->lines,
                    'auto_post' => true,
                ];

                $accountingService->createJournalEntry($data);
                
                DB::commit();
                session()->flash('message', 'Jurnal berhasil disimpan dan diposting.');
            }

            return redirect()->route('accounting.journals.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('system', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.accounting.journal-entry-form')->layout('layouts.app');
    }
}
