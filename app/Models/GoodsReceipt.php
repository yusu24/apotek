<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AccountingService;

class GoodsReceipt extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($goodsReceipt) {
            // Auto-post journal entry for goods receipt
            try {
                $accountingService = new AccountingService();
                $accountingService->postPurchaseJournal($goodsReceipt->id);
            } catch (\Exception $e) {
                \Log::error('Failed to post purchase journal: ' . $e->getMessage());
            }
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
