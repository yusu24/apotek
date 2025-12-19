<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Analgesik & Antipiretik',
            'Antibiotik',
            'Antiinflamasi',
            'Antihistamin',
            'Antasida & Antiulcer',
            'Antidiabetes',
            'Antihipertensi',
            'Antikolesterol',
            'Antiasma & PPOK',
            'Antidepresan & Psikiatri',
            'Antijamur',
            'Antivirus',
            'Vitamin & Suplemen',
            'Imunomodulator',
            'Antiseptik & Disinfektan',
            'Obat Kulit & Dermatologi',
            'Obat Mata',
            'Obat Telinga',
            'Obat Hidung',
            'Obat Saluran Cerna',
            'Obat Saluran Pernapasan',
            'Obat Jantung & Pembuluh Darah',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
        }
    }
}
