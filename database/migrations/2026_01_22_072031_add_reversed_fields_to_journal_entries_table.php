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
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->boolean('is_reversed')->default(false)->after('is_posted');
            $table->unsignedBigInteger('reversed_by')->nullable()->after('is_reversed')->comment('ID of reversal journal entry');
            
            $table->foreign('reversed_by')->references('id')->on('journal_entries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['reversed_by']);
            $table->dropColumn(['is_reversed', 'reversed_by']);
        });
    }
};
