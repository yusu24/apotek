<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivablePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'receivable_id',
        'user_id',
        'account_id', // Added
        'amount',
        'payment_method',
        'notes',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function journalEntries()
    {
        return \App\Models\JournalEntry::where('source', 'receivable_payment')->where('source_id', $this->id);
    }
}
