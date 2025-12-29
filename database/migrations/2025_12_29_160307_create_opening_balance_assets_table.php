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
        Schema::create('opening_balance_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opening_balance_id')->constrained('opening_balances')->onDelete('cascade');
            $table->string('asset_name');
            $table->decimal('amount', 15, 2);
            $table->date('acquisition_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_balance_assets');
    }
};
