<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate to avoid duplicates if re-seeding without fresh
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Supplier::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $suppliers = [
            [
                'name' => 'PT. Anugrah Argon Medica',
                'contact_person' => 'Budi Santoso',
                'phone' => '021-12345678',
                'address' => 'Jl. Titanium Raya No. 12, Jakarta Timur',
            ],
            [
                'name' => 'PT. Kimia Farma Trading & Distribution',
                'contact_person' => 'Siti Aminah',
                'phone' => '021-87654321',
                'address' => 'Jl. Budi Utomo No. 1, Jakarta Pusat',
            ],
            [
                'name' => 'PT.  Enseval Putera Megatrading Tbk',
                'contact_person' => 'Andi Wijaya',
                'phone' => '021-55566677',
                'address' => 'Jl. Pulo Lentut No. 10, Jakarta Timur',
            ],
            [
                'name' => 'PT. Parit Padang Global',
                'contact_person' => 'Dewi Sartika',
                'phone' => '021-99887766',
                'address' => 'Jl. Rawa Gelam V, Jakarta Timur',
            ],
            [
                'name' => 'PT. Bina San Prima',
                'contact_person' => 'Eko Prasetyo',
                'phone' => '021-44332211',
                'address' => 'Jl. Gatot Subroto No. 45, Bandung',
            ],
            [
                'name' => 'PT. United Dico Citas',
                'contact_person' => 'Hendra Setiawan',
                'phone' => '021-66554433',
                'address' => 'Jl. Johar No. 20, Jakarta Pusat',
            ],
            [
                'name' => 'PT. Antar Mitra Sembada',
                'contact_person' => 'Lestari',
                'phone' => '021-22334455',
                'address' => 'Jl. Pos Pengumben Raya, Jakarta Barat',
            ],
            [
                'name' => 'PT. Combi Putra',
                'contact_person' => 'Rahmat Hidayat',
                'phone' => '021-77889900',
                'address' => 'Jl. Tanah Abang II, Jakarta Pusat',
            ]
        ];

        foreach ($suppliers as $data) {
            Supplier::create($data);
        }
    }
}
