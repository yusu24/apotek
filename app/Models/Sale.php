<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AccountingService;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'invoice_no', 'date', 'total_amount', 'discount', 
        'service_charge_amount', 'service_charge_percentage', 'tax', 
        'dpp', 'ppn_mode', 'order_mode', 'rounding', 'grand_total', 
        'payment_method', 'cash_amount', 'change_amount', 'notes', 'status'
    ];

    protected $casts = [
        'total_amount' => 'float',
        'tax' => 'float',
        'discount' => 'float',
        'grand_total' => 'float',
        'cash_amount' => 'float',
        'change_amount' => 'float',
        'date' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($sale) {
            // Auto-post journal entry for completed sales
            if ($sale->status === 'completed') {
                try {
                    $accountingService = new AccountingService();
                    $accountingService->postSaleJournal($sale->id);
                } catch (\Exception $e) {
                    \Log::error('Failed to post sale journal: ' . $e->getMessage());
                }
            }
        });

        static::updated(function ($sale) {
            // Auto-post journal entry when status changes to completed
            if ($sale->isDirty('status') && $sale->status === 'completed') {
                try {
                    $accountingService = new AccountingService();
                    $accountingService->postSaleJournal($sale->id);
                } catch (\Exception $e) {
                    \Log::error('Failed to post sale journal: ' . $e->getMessage());
                }
            }
        });
    }

    public function saleItems()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function journalEntries()
    {
        return \App\Models\JournalEntry::where('source', 'sale')->where('source_id', $this->id);
    }
}
