<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_number',
        'date',
        'description',
        'source',
        'source_id',
        'user_id',
        'is_posted',
    ];

    protected $casts = [
        'date' => 'date',
        'is_posted' => 'boolean',
    ];

    // Relationships
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    public function scopeUnposted($query)
    {
        return $query->where('is_posted', false);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Methods
    public static function generateEntryNumber(string $date = null): string
    {
        $date = $date ?? now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'JE-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Post journal entry - update account balances
     */
    public function post(): bool
    {
        if ($this->is_posted) {
            return false; // Already posted
        }

        // Validate debit = credit
        $totalDebit = $this->lines()->sum('debit');
        $totalCredit = $this->lines()->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \Exception('Jurnal tidak balance: Debit != Credit');
        }

        DB::beginTransaction();
        try {
            // Update account balances
            foreach ($this->lines as $line) {
                if ($line->debit > 0) {
                    $line->account->updateBalance($line->debit, 'debit');
                }
                if ($line->credit > 0) {
                    $line->account->updateBalance($line->credit, 'credit');
                }
            }

            // Mark as posted
            $this->update(['is_posted' => true]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse journal entry (unpost)
     */
    public function reverse(): bool
    {
        if (!$this->is_posted) {
            return false;
        }

        DB::beginTransaction();
        try {
            // Reverse account balances
            foreach ($this->lines as $line) {
                if ($line->debit > 0) {
                    $line->account->updateBalance($line->debit, 'credit'); // Reverse
                }
                if ($line->credit > 0) {
                    $line->account->updateBalance($line->credit, 'debit'); // Reverse
                }
            }

            // Mark as unposted
            $this->update(['is_posted' => false]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if journal is balanced
     */
    public function isBalanced(): bool
    {
        $totalDebit = $this->lines()->sum('debit');
        $totalCredit = $this->lines()->sum('credit');
        return abs($totalDebit - $totalCredit) < 0.01;
    }

    public function getTotalDebitAttribute(): float
    {
        return $this->lines()->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return $this->lines()->sum('credit');
    }
}
