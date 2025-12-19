<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'store_name' => 'APOTEK SEHAT',
            'store_address' => 'Jl. Kesehatan No. 123, Jakarta',
            'store_phone' => '(021) 1234-5678',
            'store_email' => 'info@apoteksehat.com',
            'store_tax_id' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
