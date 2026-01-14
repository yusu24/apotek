<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'sales_return', 'purchase_return', 'supplier_payment' to the enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source ENUM('sale', 'purchase', 'stock_adjustment', 'manual', 'opening_balance', 'expense', 'supplier_payment', 'receivable_payment', 'payable_payment', 'sales_return', 'purchase_return') DEFAULT 'manual'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous enum list
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source ENUM('sale', 'purchase', 'stock_adjustment', 'manual', 'opening_balance', 'expense') DEFAULT 'manual'");
    }
};
