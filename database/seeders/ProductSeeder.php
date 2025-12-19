<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Batch;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables to ensure clean slate
        Batch::truncate();
        Product::truncate();
        Category::truncate();
        Unit::truncate();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Categories
        $categories = [
            'Obat Bebas' => 'blue',
            'Obat Keras' => 'red',
            'Obat Bebas Terbatas' => 'blue',
            'Vitamin & Suplemen' => 'green',
            'Alat Kesehatan' => 'gray', 
            'Ibu & Anak' => 'pink',
            'Herbal' => 'green',
        ];

        $categoryIds = [];
        foreach ($categories as $name => $color) {
            $cat = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
            $categoryIds[$name] = $cat->id;
        }

        // 2. Create Units
        $unitNames = ['Strip', 'Botol', 'Box', 'Tube', 'Pcs', 'Sachet', 'Tablet'];
        $unitIds = [];
        foreach ($unitNames as $name) {
            $unit = Unit::create(['name' => $name]);
            $unitIds[$name] = $unit->id;
        }

        // 3. Define Indonesian Products
        $products = [
            [
                'name' => 'Paracetamol 500mg',
                'category' => 'Obat Bebas',
                'unit' => 'Strip',
                'sell_price' => 3500,
                'min_stock' => 10,
                'description' => 'Obat penurun demam dan pereda nyeri.',
                'buy_price' => 2500, // For batch
            ],
            [
                'name' => 'Panadol Extra',
                'category' => 'Obat Bebas',
                'unit' => 'Strip',
                'sell_price' => 14500,
                'min_stock' => 10,
                'description' => 'Meredakan sakit kepala dan nyeri.',
                'buy_price' => 12000,
            ],
            [
                'name' => 'Bodrex Sakit Kepala',
                'category' => 'Obat Bebas',
                'unit' => 'Strip',
                'sell_price' => 5000,
                'min_stock' => 20,
                'description' => 'Obat sakit kepala terpercaya.',
                'buy_price' => 3500,
            ],
            [
                'name' => 'Amoxicillin 500mg',
                'category' => 'Obat Keras',
                'unit' => 'Strip',
                'sell_price' => 8000,
                'min_stock' => 5,
                'description' => 'Antibiotik untuk infeksi bakteri.',
                'buy_price' => 5000,
            ],
            [
                'name' => 'Sanmol Sirup 60ml',
                'category' => 'Obat Bebas',
                'unit' => 'Botol',
                'sell_price' => 22000,
                'min_stock' => 5,
                'description' => 'Penurun panas untuk anak-anak.',
                'buy_price' => 18000,
            ],
             [
                'name' => 'Tolak Angin Cair',
                'category' => 'Herbal',
                'unit' => 'Sachet',
                'sell_price' => 4500,
                'min_stock' => 50,
                'description' => 'Obat herbal untuk masuk angin.',
                'buy_price' => 3000,
            ],
            [
                'name' => 'Promag Tablet',
                'category' => 'Obat Bebas',
                'unit' => 'Strip',
                'sell_price' => 9000,
                'min_stock' => 20,
                'description' => 'Mengatasi sakit maag dan kembung.',
                'buy_price' => 7000,
            ],
            [
                'name' => 'Mylanta Cair 50ml',
                'category' => 'Obat Bebas',
                'unit' => 'Botol',
                'sell_price' => 18000,
                'min_stock' => 10,
                'description' => 'Obat maag cair cepat redakan nyeri lambung.',
                'buy_price' => 14000,
            ],
            [
                'name' => 'Betadine Antiseptic 15ml',
                'category' => 'Alat Kesehatan',
                'unit' => 'Botol',
                'sell_price' => 25000,
                'min_stock' => 5,
                'description' => 'Obat luka antiseptik.',
                'buy_price' => 20000,
            ],
            [
                'name' => 'Insto Regular 7.5ml',
                'category' => 'Obat Bebas Terbatas',
                'unit' => 'Botol',
                'sell_price' => 16500,
                'min_stock' => 10,
                'description' => 'Obat tetes mata untuk iritasi ringan.',
                'buy_price' => 13000,
            ],
             [
                'name' => 'Enervon-C Multivitamin',
                'category' => 'Vitamin & Suplemen',
                'unit' => 'Strip',
                'sell_price' => 6500,
                'min_stock' => 15,
                'description' => 'Suplemen vitamin C dan B kompleks.',
                'buy_price' => 4500,
            ],
            [
                'name' => 'Imboost Force',
                'category' => 'Vitamin & Suplemen',
                'unit' => 'Strip',
                'sell_price' => 45000,
                'min_stock' => 5,
                'description' => 'Meningkatkan daya tahan tubuh.',
                'buy_price' => 38000,
            ],
             [
                'name' => 'Minyak Kayu Putih Cap Lang 60ml',
                'category' => 'Obat Bebas',
                'unit' => 'Botol',
                'sell_price' => 28000,
                'min_stock' => 10,
                'description' => 'Minyak kayu putih asli.',
                'buy_price' => 23000,
            ],
            [
                'name' => 'Dettol Antiseptic Liquid 95ml',
                'category' => 'Alat Kesehatan',
                'unit' => 'Botol',
                'sell_price' => 35000,
                'min_stock' => 5,
                'description' => 'Cairan antiseptik pembunuh kuman.',
                'buy_price' => 28000,
            ],
             [
                'name' => 'Diapet Kapsul',
                'category' => 'Obat Bebas',
                'unit' => 'Strip',
                'sell_price' => 3500,
                'min_stock' => 20,
                'description' => 'Mengatasi diare.',
                'buy_price' => 2500,
            ],
             [
                'name' => 'Entrostop',
                'category' => 'Obat Bebas',
                'unit' => 'Strip',
                'sell_price' => 8500,
                'min_stock' => 20,
                'description' => 'Obat diare anak dan dewasa.',
                'buy_price' => 6000,
            ],
             [
                'name' => 'Decolgen',
                'category' => 'Obat Bebas Terbatas',
                'unit' => 'Strip',
                'sell_price' => 4000,
                'min_stock' => 20,
                'description' => 'Obat flu dan batuk.',
                'buy_price' => 2800,
            ],
             [
                'name' => 'Ultraflu',
                'category' => 'Obat Bebas',
                'unit' => 'Strip',
                'sell_price' => 3500,
                'min_stock' => 30,
                'description' => 'Meredakan gejala flu.',
                'buy_price' => 2500,
            ],
            [
                'name' => 'Siladex Mucolytic 60ml',
                'category' => 'Obat Bebas Terbatas',
                'unit' => 'Botol',
                'sell_price' => 17000,
                'min_stock' => 5,
                'description' => 'Obat batuk berdahak.',
                'buy_price' => 13500,
            ],
            [
                'name' => 'Salonpas Koyo',
                'category' => 'Obat Bebas',
                'unit' => 'Sachet',
                'sell_price' => 7000,
                'min_stock' => 50,
                'description' => 'Koyo pereda nyeri otot.',
                'buy_price' => 5000,
            ],
        ];

        // 4. Insert Products and Batches
        foreach ($products as $p) {
            $product = Product::create([
                'category_id' => $categoryIds[$p['category']],
                'unit_id' => $unitIds[$p['unit']],
                'name' => $p['name'],
                'slug' => Str::slug($p['name']) . '-' . Str::random(5),
                'barcode' => rand(1000000000000, 9999999999999), // Dummy barcode
                'min_stock' => $p['min_stock'],
                'sell_price' => $p['sell_price'],
                'description' => $p['description'],
            ]);

            // Create 1-2 batches for each product
            $numBatches = rand(1, 2);
            for ($i = 0; $i < $numBatches; $i++) {
                $stockIn = rand(20, 100);
                Batch::create([
                    'product_id' => $product->id,
                    'batch_no' => 'BATCH-' . strtoupper(Str::random(6)),
                    'expired_date' => Carbon::now()->addMonths(rand(6, 24)),
                    'stock_in' => $stockIn,
                    'stock_current' => $stockIn, // Assuming full stock initially
                    'buy_price' => $p['buy_price'],
                ]);
            }
        }
    }
}
