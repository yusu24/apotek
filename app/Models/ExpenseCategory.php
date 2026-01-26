<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Auto-format name to Title Case
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = \Illuminate\Support\Str::title($value);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category', 'name');
    }
}
