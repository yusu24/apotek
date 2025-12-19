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
            $table->decimal('subtotal', 15, 2)->after('date')->default(0);
            $table->decimal('rounding', 15, 2)->after('grand_total')->default(0);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('discount_amount', 15, 2)->after('sell_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'rounding']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['discount_amount']);
        });
    }
};
