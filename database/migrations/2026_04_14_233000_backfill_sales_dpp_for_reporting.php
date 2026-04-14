<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Memperbaiki data historis kolom 'dpp' (Dasar Pengenaan Pajak / Penjualan Bersih)
     * agar laporan keuangan (Web, PDF, Excel) tampil akurat di VPS.
     */
    public function up(): void
    {
        // Periksa apakah tabel sales ada
        if (Schema::hasTable('sales')) {
            // Update dpp = grand_total - tax - rounding bagi data yang masih nol
            // PERHATIKAN: Ini hanya MENGUPDATE isi kolom, TIDAK MENGHAPUS tabel atau database.
            DB::table('sales')
                ->where('dpp', 0)
                ->where('grand_total', '>', 0)
                ->update([
                    'dpp' => DB::raw('grand_total - tax - rounding')
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu melakukan apa-apa saat rollback untuk data repair
    }
};
