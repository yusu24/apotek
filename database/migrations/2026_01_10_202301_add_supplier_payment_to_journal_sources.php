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
        DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source ENUM('sale', 'purchase', 'stock_adjustment', 'manual', 'opening_balance', 'expense', 'supplier_payment') DEFAULT 'manual'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source ENUM('sale', 'purchase', 'stock_adjustment', 'manual', 'opening_balance', 'expense') DEFAULT 'manual'");
    }
};
