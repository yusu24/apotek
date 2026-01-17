<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedAsset extends Model
{
    protected $fillable = [
        'asset_code',
        'asset_name',
        'tax_group',
        'method',
        'acquisition_date',
        'acquisition_cost',
        'salvage_value',
        'useful_life_years',
        'asset_account_id',
        'accumulated_depreciation_account_id',
        'depreciation_expense_account_id',
        'is_active',
        'description',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'useful_life_years' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assetAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'asset_account_id');
    }

    public function accumulatedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accumulated_depreciation_account_id');
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'depreciation_expense_account_id');
    }

    public function depreciations(): HasMany
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    public function getTotalDepreciationAttribute(): float
    {
        return (float) $this->depreciations()->sum('amount');
    }

    public function getBookValueAttribute(): float
    {
        return (float) $this->acquisition_cost - $this->total_depreciation;
    }

    public static function getTaxGroups(): array
    {
        return [
            '1' => ['label' => 'Kelompok 1 (4 Tahun)', 'life' => 4, 'sl_rate' => 0.25, 'db_rate' => 0.50],
            '2' => ['label' => 'Kelompok 2 (8 Tahun)', 'life' => 8, 'sl_rate' => 0.125, 'db_rate' => 0.25],
            '3' => ['label' => 'Kelompok 3 (16 Tahun)', 'life' => 16, 'sl_rate' => 0.0625, 'db_rate' => 0.125],
            '4' => ['label' => 'Kelompok 4 (20 Tahun)', 'life' => 20, 'sl_rate' => 0.05, 'db_rate' => 0.10],
            'building_permanent' => ['label' => 'Bangunan Permanen (20 Tahun)', 'life' => 20, 'sl_rate' => 0.05, 'db_rate' => 0.05],
            'building_non_permanent' => ['label' => 'Bangunan Tidak Permanen (10 Tahun)', 'life' => 10, 'sl_rate' => 0.10, 'db_rate' => 0.10],
        ];
    }
}
