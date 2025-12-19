<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Batch;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dummy Master Data
        // Use firstOrCreate or just create new ones. Since factories create unique data usually, create is fine.
        $categories = Category::factory(5)->create();
        $units = Unit::factory(5)->create();
        $suppliers = Supplier::factory(5)->create();

        // Dummy Products & Batches
        $products = Product::factory(20)
            ->recycle($categories)
            ->recycle($units)
            ->create();

        foreach ($products as $product) {
            Batch::factory(rand(1, 3))->create([
                'product_id' => $product->id,
            ]);
        }
    }
}
