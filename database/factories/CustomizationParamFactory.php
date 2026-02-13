<?php

namespace Database\Factories;

use App\Models\CustomizationParam;
use App\Models\CustomizationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomizationParam>
 */
class CustomizationParamFactory extends Factory
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
            'customization_category_id' => CustomizationCategory::factory(),
            'key' => fake()->words(2, true),
            'value' => '',
            'order' => 0,
            'option1' => null,
            'option2' => null,
        ];
    }
} 