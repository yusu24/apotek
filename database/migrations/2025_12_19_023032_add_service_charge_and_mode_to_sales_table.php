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
            $table->decimal('service_charge_amount', 15, 2)->default(0)->after('discount');
            $table->decimal('service_charge_percentage', 5, 2)->default(0)->after('service_charge_amount');
            $table->string('order_mode')->default('In')->after('service_charge_percentage'); // 'In' for Dine-in/Instore, 'Out' for Takeaway
            $table->string('status')->default('completed')->after('order_mode'); // 'draft', 'pending', 'completed', 'canceled'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['service_charge_amount', 'service_charge_percentage', 'order_mode', 'status']);
        });
    }
};
