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
            $table->string('patient_name')->nullable();
            $table->string('patient_doctor_name')->nullable();
            $table->date('patient_birth_date')->nullable();
            $table->text('patient_address')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['patient_name', 'patient_doctor_name', 'patient_birth_date', 'patient_address', 'patient_phone', 'patient_email']);
        });
    }
};
