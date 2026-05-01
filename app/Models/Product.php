<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'unit_id', 'name', 'slug', 'barcode', 
        'min_stock', 'sell_price', 'purchase_price', 'purchase_price_updated_at', 
        'description', 'image_path'
    ];

    protected $casts = [
        'min_stock' => 'integer',
        'sell_price' => 'float',
        'purchase_price' => 'float',
        'purchase_price_updated_at' => 'datetime',
    ];

    // Auto-format name to Title Case
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = \Illuminate\Support\Str::title($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function unitConversions()
    {
        return $this->hasMany(UnitConversion::class);
    }

    /**
     * Scope for products with low stock (based on min_stock)
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('(select coalesce(sum(stock_current), 0) from batches where batches.product_id = products.id) <= products.min_stock')
                     ->whereRaw('(select coalesce(sum(stock_current), 0) from batches where batches.product_id = products.id) > 0');
    }

    /**
     * Scope for products that are out of stock
     */
    public function scopeOutOfStock($query)
    {
        return $query->whereRaw('(select coalesce(sum(stock_current), 0) from batches where batches.product_id = products.id) <= 0');
    }
}
