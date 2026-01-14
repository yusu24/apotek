<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AccountingService;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_id', 'invoice_no', 'date', 'total_amount', 'discount', 
        'service_charge_amount', 'service_charge_percentage', 'tax', 
        'dpp', 'ppn_mode', 'order_mode', 'rounding', 'grand_total', 
        'payment_method', 'cash_amount', 'change_amount', 'notes', 'status',
        // Patient Information
        'patient_name', 'patient_doctor_name', 'patient_birth_date', 
        'patient_address', 'patient_phone', 'patient_email'
    ];

    protected $casts = [
        'total_amount' => 'float',
        'tax' => 'float',
        'discount' => 'float',
        'grand_total' => 'float',
        'cash_amount' => 'float',
        'change_amount' => 'float',
        'date' => 'datetime',
        'patient_birth_date' => 'date',
    ];


    public function saleItems()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function receivables()
    {
        return $this->hasOne(\App\Models\Receivable::class);
    }
    
    public function journalEntries()
    {
        return \App\Models\JournalEntry::where('source', 'sale')->where('source_id', $this->id);
    }
}
