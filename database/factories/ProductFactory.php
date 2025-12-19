<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Unit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'unit_id' => Unit::inRandomOrder()->first()?->id ?? Unit::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'barcode' => $this->faker->unique()->ean13(),
            'min_stock' => $this->faker->numberBetween(5, 10),
            'sell_price' => $this->faker->numberBetween(5, 500) * 1000,
            'description' => $this->faker->sentence(),
        ];
    }
}
