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
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'dpp')) $table->decimal('dpp', 15, 2)->default(0);
            if (!Schema::hasColumn('sales', 'ppn_mode')) $table->string('ppn_mode')->default('off');
            if (!Schema::hasColumn('sales', 'rounding')) $table->decimal('rounding', 15, 2)->default(0);
            if (!Schema::hasColumn('sales', 'service_charge_amount')) $table->decimal('service_charge_amount', 15, 2)->default(0);
            if (!Schema::hasColumn('sales', 'service_charge_percentage')) $table->decimal('service_charge_percentage', 5, 2)->default(0);
            if (!Schema::hasColumn('sales', 'notes')) $table->text('notes')->nullable();
            if (!Schema::hasColumn('sales', 'status')) $table->string('status')->default('completed');
            if (!Schema::hasColumn('sales', 'order_mode')) $table->string('order_mode')->default('Out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'dpp', 
                'ppn_mode', 
                'rounding', 
                'service_charge_amount', 
                'service_charge_percentage', 
                'notes', 
                'status',
                'order_mode'
            ]);
        });
    }
};
