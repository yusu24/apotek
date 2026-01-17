<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDepreciation extends Model
{
    protected $fillable = [
        'fixed_asset_id',
        'journal_entry_id',
        'period_date',
        'amount',
        'book_value_after',
    ];

    protected $casts = [
        'period_date' => 'date',
        'amount' => 'decimal:2',
        'book_value_after' => 'decimal:2',
    ];

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry.class);
    }
}
