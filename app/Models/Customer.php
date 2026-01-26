<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'address'];

    // Auto-format name to Title Case
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = \Illuminate\Support\Str::title($value);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }
}
