<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes for FIFO
    public function scopeValid($query)
    {
        return $query->where('stock_current', '>', 0)
                     ->whereDate('expired_date', '>=', now())
                     ->orderBy('expired_date', 'asc')
                     ->orderBy('created_at', 'asc');
    }
}
