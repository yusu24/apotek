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
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('normal_balance', ['debit', 'credit'])->after('category')->nullable();
        });

        // Populate normal_balance based on category
        DB::statement("
            UPDATE accounts 
            SET normal_balance = CASE 
                WHEN category IN ('asset', 'expense', 'cost_of_goods_sold') THEN 'debit'
                WHEN category IN ('liability', 'equity', 'revenue') THEN 'credit'
                ELSE 'debit'
            END
        ");

        // Make it non-nullable after populating
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('normal_balance', ['debit', 'credit'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('normal_balance');
        });
    }
};
