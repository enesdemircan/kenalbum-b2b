<?php

namespace Database\Factories;

use App\Models\CustomizationPivotParam;
use App\Models\Product;
use App\Models\CustomizationParam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomizationPivotParam>
 */
class CustomizationPivotParamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'params_id' => CustomizationParam::factory(),
            'option1' => null,
            'option2' => null,
        ];
    }
} 