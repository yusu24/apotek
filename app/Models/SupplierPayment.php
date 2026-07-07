<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $guarded = [];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(\App\Models\JournalEntry::class, 'source_id')->where('source', 'supplier_payment');
    }
}
