<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'Tablet',
            'Kapsul',
            'Pil',
            'Kaplet',
            'Sirup',
            'Botol',
            'Vial',
            'Ampul',
            'Sachet',
            'Strip',
            'Blister',
            'Box / Dus',
            'Tube',
            'Pcs',
            'Unit',
        ];

        foreach ($units as $name) {
            Unit::firstOrCreate(
                ['name' => $name]
            );
        }
    }
}
