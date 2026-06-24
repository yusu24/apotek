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
        // Add 'opening_balance' and 'expense' to the enum
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source ENUM('sale', 'purchase', 'stock_adjustment', 'manual', 'opening_balance', 'expense') DEFAULT 'manual'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum list (WARNING: data with new enum values will be truncated or invalid depending on strict mode)
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source ENUM('sale', 'purchase', 'stock_adjustment', 'manual') DEFAULT 'manual'");
        }
    }
};
