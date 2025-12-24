<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'product_id', 'batch_id', 'quantity', 'sell_price', 
        'discount_amount', 'subtotal', 'notes'
    ];

    protected $casts = [
        'sell_price' => 'float',
        'subtotal' => 'float',
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(\App\Models\Batch::class);
    }

    public function sale()
    {
        return $this->belongsTo(\App\Models\Sale::class);
    }
}
