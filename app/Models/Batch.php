<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'batch_no', 'expired_date', 'stock_in', 
        'stock_current', 'buy_price'
    ];

    protected $casts = [
        'buy_price' => 'float',
        'stock_initial' => 'integer',
        'stock_current' => 'integer',
        'expired_date' => 'date',
    ];

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
