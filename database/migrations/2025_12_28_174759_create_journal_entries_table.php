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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique(); // JE-20251229-001
            $table->date('date');
            $table->string('description');
            $table->enum('source', ['sale', 'purchase', 'stock_adjustment', 'manual'])->default('manual');
            $table->unsignedBigInteger('source_id')->nullable(); // ID from source table
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_posted')->default(false); // true when journal is posted
            $table->timestamps();
            
            $table->index(['date', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
