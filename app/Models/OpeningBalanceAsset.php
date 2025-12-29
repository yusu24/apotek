<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpeningBalanceAsset extends Model
{
    protected $fillable = [
        'opening_balance_id',
        'asset_name',
        'amount',
        'acquisition_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'acquisition_date' => 'date',
    ];

    public function openingBalance(): BelongsTo
    {
        return $this->belongsTo(OpeningBalance::class);
    }
}
