<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function saleItems()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
