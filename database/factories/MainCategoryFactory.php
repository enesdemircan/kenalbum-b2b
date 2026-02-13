<?php

namespace Database\Factories;

use App\Models\MainCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MainCategory>
 */
class MainCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ust_id' => 0,
            'title' => fake()->words(2, true),
            'order' => 0,
            'option1' => null,
            'option2' => null,
        ];
    }
} 