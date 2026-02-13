<?php

namespace Database\Factories;

use App\Models\CustomizationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomizationCategory>
 */
class CustomizationCategoryFactory extends Factory
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
            'type' => fake()->randomElement(['radio', 'select', 'input']),
            'required' => fake()->boolean(),
            'order' => 0,
            'option1' => null,
            'option2' => null,
        ];
    }
} 