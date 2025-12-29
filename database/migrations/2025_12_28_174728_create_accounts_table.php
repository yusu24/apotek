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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., 1-1100
            $table->string('name'); // e.g., Kas
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('category', [
                'current_asset', 'fixed_asset', 
                'current_liability', 'long_term_liability', 
                'capital', 'sales', 'cogs', 'operating_expense', 'other'
            ]);
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_system')->default(false); // System accounts cannot be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
