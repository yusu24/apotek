<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class OpeningBalance extends Model
{
    protected $fillable = [
        'cash_amount',
        'bank_amount',
        'capital_amount',
        'journal_entry_id',
        'is_confirmed',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'capital_amount' => 'decimal:2',
        'is_confirmed' => 'boolean',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(OpeningBalanceAsset::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(OpeningBalanceDebt::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Calculate Summary
     */
    public function getSummary()
    {
        $totalAssets = $this->cash_amount + $this->bank_amount + $this->assets()->sum('amount');
        $totalLiabilities = $this->debts()->sum('amount');
        $totalEquity = $this->capital_amount;

        return [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'difference' => $totalAssets - ($totalLiabilities + $totalEquity),
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01
        ];
    }

    /**
     * Create or Update Opening Journal Entry
     */
    public function syncJournal()
    {
        $summary = $this->getSummary();
        if (!$summary['is_balanced']) {
            throw new \Exception('Neraca belum seimbang. Total Aset harus sama dengan Total Liabilitas + Ekuitas.');
        }

        DB::beginTransaction();
        try {
            // 1. Delete old journal entry if exists
            if ($this->journalEntry) {
                $this->journalEntry->reverse();
                $this->journalEntry->lines()->delete();
                $journalId = $this->journal_entry_id;
            } else {
                $journal = JournalEntry::create([
                    'entry_number' => JournalEntry::generateEntryNumber(),
                    'date' => now(),
                    'description' => 'Jurnal Pembukaan (Neraca Awal)',
                    'source' => 'opening_balance',
                    'source_id' => $this->id,
                    'user_id' => auth()->id() ?? 1,
                    'is_posted' => false,
                ]);
                $journalId = $journal->id;
                $this->update(['journal_entry_id' => $journalId]);
            }

            $journal = JournalEntry::find($journalId);
            $journal->description = 'Jurnal Pembukaan (Neraca Awal)';
            $journal->save();

            // 2. Create Journal Lines
            $lines = [];

            // DEBITS (Assets)
            if ($this->cash_amount > 0) {
                $lines[] = ['account_id' => Account::where('code', '1-1100')->first()->id, 'debit' => $this->cash_amount, 'credit' => 0];
            }
            if ($this->bank_amount > 0) {
                $lines[] = ['account_id' => Account::where('code', '1-1200')->first()->id, 'debit' => $this->bank_amount, 'credit' => 0];
            }
            foreach ($this->assets as $asset) {
                if ($asset->amount > 0) {
                    $lines[] = ['account_id' => Account::where('code', '1-2000')->first()->id, 'debit' => $asset->amount, 'credit' => 0];
                }
            }

            // CREDITS (Liabilities & Equity)
            if ($this->capital_amount > 0) {
                $lines[] = ['account_id' => Account::where('code', '3-1000')->first()->id, 'debit' => 0, 'credit' => $this->capital_amount];
            }
            foreach ($this->debts as $debt) {
                if ($debt->amount > 0) {
                    $code = $debt->debt_type === 'supplier' ? '2-1100' : '2-2000';
                    $lines[] = ['account_id' => Account::where('code', $code)->first()->id, 'debit' => 0, 'credit' => $debt->amount];
                }
            }

            foreach ($lines as $line) {
                $journal->lines()->create($line);
            }

            // 3. Post Journal
            $journal->post();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
