<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'unit_id', 'name', 'slug', 'barcode', 
        'min_stock', 'sell_price', 'description', 'image_path'
    ];

    protected $casts = [
        'min_stock' => 'integer',
        'sell_price' => 'float',
    ];

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
}
