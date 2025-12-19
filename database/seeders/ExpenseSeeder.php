<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->warn('Pastikan sudah ada data user sebelum menjalankan seeder ini.');
            return;
        }

        $categories = [
            'Operasional' => [
                'Listrik',
                'Air',
                'Internet',
                'Telepon',
                'Kebersihan',
            ],
            'Gaji' => [
                'Gaji Karyawan',
                'Bonus',
                'Lembur',
            ],
            'Pemeliharaan' => [
                'Perbaikan AC',
                'Perbaikan Komputer',
                'Perbaikan Furniture',
                'Service Printer',
            ],
            'Perlengkapan' => [
                'ATK',
                'Plastik Kemasan',
                'Struk Thermal',
                'Label Harga',
            ],
            'Transportasi' => [
                'Bensin',
                'Parkir',
                'Tol',
                'Ojek Online',
            ],
            'Lain-lain' => [
                'Makan Siang Karyawan',
                'Konsumsi Rapat',
                'Donasi',
            ],
        ];

        // Generate expenses untuk 30 hari terakhir
        $startDate = Carbon::now()->subDays(30);
        
        for ($day = 0; $day < 30; $day++) {
            $date = $startDate->copy()->addDays($day);
            
            // Random 1-4 pengeluaran per hari
            $expensesPerDay = rand(1, 4);
            
            for ($i = 0; $i < $expensesPerDay; $i++) {
                $category = array_rand($categories);
                $descriptions = $categories[$category];
                $description = $descriptions[array_rand($descriptions)];
                
                // Amount berdasarkan kategori
                $amount = match($category) {
                    'Gaji' => rand(3000000, 5000000),
                    'Operasional' => rand(100000, 500000),
                    'Pemeliharaan' => rand(200000, 1000000),
                    'Perlengkapan' => rand(50000, 300000),
                    'Transportasi' => rand(20000, 150000),
                    'Lain-lain' => rand(50000, 200000),
                    default => rand(50000, 500000),
                };
                
                Expense::create([
                    'date' => $date,
                    'description' => $description,
                    'amount' => $amount,
                    'category' => $category,
                    'user_id' => $user->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
        
        $this->command->info('✓ Berhasil membuat ' . (30 * 2) . ' data pengeluaran dummy untuk 30 hari terakhir');
    }
}
