<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDraft extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'items' => 'array',
        'totals' => 'array',
    ];
}
