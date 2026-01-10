<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AccountingService;

class GoodsReceipt extends Model
{
    protected $guarded = [];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];


    public function getPaymentStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'Lunas',
            'partial' => 'Setengah',
            'pending' => 'Hutang',
            default => 'Hutang',
        };
    }

    public function getPaymentStatusColorAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'green',
            'partial' => 'yellow',
            'pending' => 'red',
            default => 'gray',
        };
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function updatePaymentStatus()
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;
        
        if ($totalPaid >= $this->total_amount) {
            $this->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'pending';
        }
        
        $this->save();
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

    public function journalEntries()
    {
        return \App\Models\JournalEntry::where('source', 'purchase')->where('source_id', $this->id);
    }
}
