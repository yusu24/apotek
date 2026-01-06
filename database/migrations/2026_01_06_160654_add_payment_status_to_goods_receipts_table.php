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
            $table->decimal('total_amount', 15, 2)->default(0)->after('due_date_weeks');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('total_amount');
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending')->after('paid_amount');
            $table->date('due_date')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'paid_amount', 'payment_status', 'due_date']);
        });
    }
};
