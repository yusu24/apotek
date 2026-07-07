<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'date', 'description', 'amount', 'category', 'type', 'user_id', 'account_id'
    ];

    protected $casts = [
        'amount' => 'float',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(\App\Models\JournalEntry::class, 'source_id')->where('source', 'expense');
    }
}
