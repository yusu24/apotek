<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Batch>
 */
class BatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stock = $this->faker->numberBetween(10, 100);
        return [
            'product_id' => Product::factory(),
            'batch_no' => strtoupper($this->faker->bothify('BATCH-###')),
            'expired_date' => $this->faker->dateTimeBetween('-1 month', '+2 years'),
            'stock_in' => $stock,
            'stock_current' => $stock,
            'buy_price' => $this->faker->numberBetween(1000, 50000),
        ];
    }
}
