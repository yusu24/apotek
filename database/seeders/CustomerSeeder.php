<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Pak Hendro Santoso',
                'phone' => '0812-3456-7890',
                'address' => 'Jl. Merdeka No. 12, Kel. Gambir, Jakarta Pusat',
            ],
            [
                'name' => 'Ibu Ratna Sari',
                'phone' => '0813-8899-7711',
                'address' => 'Jl. Tebet Barat Dalam VII No. 4, Jakarta Selatan',
            ],
            [
                'name' => 'dr. Bambang Sudiro, Sp.A',
                'phone' => '0811-2233-4455',
                'address' => 'Praktek Dokter Spesialis Anak, Jl. Fatmawati No. 88, Jakarta Selatan',
            ],
            [
                'name' => 'Ibu Siti Aminah',
                'phone' => '0857-1234-9876',
                'address' => 'Komplek Griya Asri Blok C2 No. 15, Depok',
            ],
            [
                'name' => 'Budi Prasetyo',
                'phone' => '0878-5544-3322',
                'address' => 'Jl. Kemang Raya No. 45A, Jakarta Selatan',
            ],
            [
                'name' => 'dr. Rini Kusuma, Sp.PD',
                'phone' => '0812-9988-7766',
                'address' => 'Klinik Penyakit Dalam, Jl. Diponegoro No. 23, Bandung',
            ],
            [
                'name' => 'Rizky Firmansyah',
                'phone' => '0819-0102-0304',
                'address' => 'Jl. Margonda Raya No. 200, Depok',
            ],
            [
                'name' => 'Dewi Sartika',
                'phone' => '0821-4567-8901',
                'address' => 'Perumahan Pondok Indah Blok D3 No. 8, Jakarta Selatan',
            ],
            [
                'name' => 'Agus Salim',
                'phone' => '0852-3344-5566',
                'address' => 'Jl. Daan Mogot Km. 11 No. 5, Jakarta Barat',
            ],
            [
                'name' => 'Sri Wahyuni',
                'phone' => '0813-7788-9900',
                'address' => 'Jl. Cempaka Putih Tengah No. 14, Jakarta Pusat',
            ],
            [
                'name' => 'Andi Wijaya',
                'phone' => '0877-6655-4433',
                'address' => 'Jl. Kaliurang Km. 5 No. 18, Yogyakarta',
            ],
            [
                'name' => 'Eko Prabowo',
                'phone' => '0812-1122-3344',
                'address' => 'Jl. Pemuda No. 76, Semarang',
            ],
            [
                'name' => 'Nina Herlina',
                'phone' => '0856-7890-1234',
                'address' => 'Jl. Pajajaran No. 34, Bogor',
            ],
            [
                'name' => 'Denny Siregar',
                'phone' => '0818-4455-6677',
                'address' => 'Jl. Boulevard Kelapa Gading Blok LA No. 2, Jakarta Utara',
            ],
            [
                'name' => 'Fitriani',
                'phone' => '0822-9900-1122',
                'address' => 'Perumahan Harapan Indah Blok GA No. 10, Bekasi',
            ],
            [
                'name' => 'Irfan Hakim',
                'phone' => '0812-8877-6655',
                'address' => 'Jl. Senopati No. 55, Kebayoran Baru, Jakarta Selatan',
            ],
            [
                'name' => 'Wulan Guritno',
                'phone' => '0813-2211-4433',
                'address' => 'Jl. Brawijaya Raya No. 18, Jakarta Selatan',
            ],
            [
                'name' => 'Dian Sastrowardoyo',
                'phone' => '0811-9988-1122',
                'address' => 'Jl. Cik Ditiro No. 29, Menteng, Jakarta Pusat',
            ],
            [
                'name' => 'Surya Saputra',
                'phone' => '0857-4433-2211',
                'address' => 'Jl. Radio Dalam Raya No. 14, Jakarta Selatan',
            ],
            [
                'name' => 'Tantri Kotak',
                'phone' => '0878-1234-5678',
                'address' => 'Jl. Sukajadi No. 89, Bandung',
            ],
            [
                'name' => 'Reza Rahadian',
                'phone' => '0812-7766-5544',
                'address' => 'Jl. Gandaria I No. 7, Jakarta Selatan',
            ],
            [
                'name' => 'Maudy Ayunda',
                'phone' => '0813-5566-7788',
                'address' => 'Jl. Teuku Umar No. 11, Menteng, Jakarta Pusat',
            ],
            [
                'name' => 'Klinik Medika Pratama (dr. Hadi)',
                'phone' => '021-7890-1234',
                'address' => 'Jl. Raya Pasar Minggu No. 40, Jakarta Selatan',
            ],
            [
                'name' => 'Puskesmas Kelurahan Menteng',
                'phone' => '021-3140-5678',
                'address' => 'Jl. Pegangsaan Barat No. 2, Jakarta Pusat',
            ],
            [
                'name' => 'Apotek Rekanan Sehat',
                'phone' => '021-5820-9988',
                'address' => 'Ruko Golden Boulevard Blok W2 No. 5, BSD City, Tangerang',
            ],
        ];

        foreach ($customers as $data) {
            Customer::firstOrCreate(
                ['name' => $data['name']],
                [
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                ]
            );
        }
    }
}
