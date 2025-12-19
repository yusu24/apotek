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
            $table->decimal('dpp', 15, 2)->default(0)->after('total_amount');
            $table->string('ppn_mode')->default('off')->after('tax');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('notes')->nullable()->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['dpp', 'ppn_mode']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
