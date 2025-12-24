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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('purchase_order_items', 'conversion_factor')) {
                $table->decimal('conversion_factor', 10, 4)->default(1);
            }
        });

        Schema::table('goods_receipt_items', function (Blueprint $table) {
            if (!Schema::hasColumn('goods_receipt_items', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('goods_receipt_items', 'conversion_factor')) {
                $table->decimal('conversion_factor', 10, 4)->default(1);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order_items', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
            if (Schema::hasColumn('purchase_order_items', 'conversion_factor')) {
                $table->dropColumn('conversion_factor');
            }
        });

        Schema::table('goods_receipt_items', function (Blueprint $table) {
            if (Schema::hasColumn('goods_receipt_items', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
            if (Schema::hasColumn('goods_receipt_items', 'conversion_factor')) {
                $table->dropColumn('conversion_factor');
            }
        });
    }
};
