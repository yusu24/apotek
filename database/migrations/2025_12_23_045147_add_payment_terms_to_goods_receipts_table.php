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
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'transfer', 'due_date'])->default('cash')->after('notes');
            $table->integer('due_date_weeks')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'due_date_weeks']);
        });
    }
};
