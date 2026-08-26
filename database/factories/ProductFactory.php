<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
        $price = fake()->randomFloat(2, 500, 2500);

        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'slug' => fake()->unique()->slug(3),
            'sku' => 'BC-'.fake()->unique()->numerify('####'),
            'short_description' => fake()->sentence(10),
            'description' => fake()->paragraph(),
            'price' => $price,
            'sale_price' => fake()->boolean(30) ? fake()->randomFloat(2, 400, $price - 10) : null,
            'stock_status' => Product::STOCK_STATUS_IN_STOCK,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (): array => ['stock_status' => Product::STOCK_STATUS_OUT_OF_STOCK]);
    }
}
