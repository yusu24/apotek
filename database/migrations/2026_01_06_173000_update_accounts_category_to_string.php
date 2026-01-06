<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change category column to string to allow more flexibility
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('category')->change();
        });

        // Update existing Kas (1-1100) and Bank (1-1200) to 'cash_bank'
        DB::table('accounts')
            ->whereIn('code', ['1-1100', '1-1200'])
            ->update(['category' => 'cash_bank']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Kas and Bank to 'current_asset'
        DB::table('accounts')
            ->whereIn('code', ['1-1100', '1-1200'])
            ->update(['category' => 'current_asset']);

        // Revert column to enum (this might be tricky depending on data, but let's try strict mode off)
        Schema::table('accounts', function (Blueprint $table) {
             // Re-defining enum involves raw SQL in some drivers, 
             // but Laravel's schema builder might handle it if data fits.
             // For safety in down(), let's just leave it as string or try to revert if sure.
             // Ideally we shouldn't revert strictly to enum if we added custom data.
             // Just leaving it as string is safer for down in dev env.
        });
    }
};
