<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\MainCategory;
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
            'title' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 100, 2000),
            'main_category_id' => MainCategory::factory(),
            'images' => fake()->imageUrl(),
            'status' => 1,
            'order' => 0,
            'price_difference_per_page' => fake()->randomFloat(2, 5, 20),
            'min_pages' => fake()->numberBetween(1, 10),
            'max_pages' => fake()->numberBetween(20, 100),
            'option1' => null,
            'option2' => null,
        ];
    }
} 