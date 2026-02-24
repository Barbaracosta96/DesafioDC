<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'purchase_price' => fake()->randomFloat(2, 10, 1000),
            'sale_price' => fake()->randomFloat(2, 20, 2000),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'min_stock' => fake()->numberBetween(5, 20),
            'status' => fake()->randomElement(['active', 'inactive']),
            // 'category_id' => Category::factory(), // If category is required
        ];
    }
}
