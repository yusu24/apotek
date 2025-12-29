<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'balance',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    // Scopes
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    // Methods
    public function updateBalance(float $amount, string $type = 'debit'): void
    {
        // Asset & Expense accounts increase with debit, decrease with credit
        // Liability, Equity & Revenue accounts increase with credit, decrease with debit
        
        $increaseOnDebit = in_array($this->type, ['asset', 'expense']);
        
        if ($type === 'debit') {
            $this->balance += $increaseOnDebit ? $amount : -$amount;
        } else {
            $this->balance += $increaseOnDebit ? -$amount : $amount;
        }
        
        $this->save();
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    /**
     * Check if account can be deleted
     */
    public function canDelete(): bool
    {
        return !$this->is_system && $this->journalEntryLines()->count() === 0;
    }
}
