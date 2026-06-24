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
        try {
            if (class_exists(\Doctrine\DBAL\Connection::class)) {
                Schema::getConnection()->getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
            }
        } catch (\Throwable $e) {}

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            if (class_exists(\Doctrine\DBAL\Connection::class)) {
                Schema::getConnection()->getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
            }
        } catch (\Throwable $e) {}

        Schema::table('sale_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
