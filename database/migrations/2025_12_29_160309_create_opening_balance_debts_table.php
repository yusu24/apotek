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
        Schema::create('opening_balance_debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opening_balance_id')->constrained('opening_balances')->onDelete('cascade');
            $table->string('debt_name');
            $table->string('debt_type'); // supplier / bank
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_balance_debts');
    }
};
