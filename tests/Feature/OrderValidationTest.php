<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\MainCategory;
use App\Models\CustomizationCategory;
use App\Models\CustomizationParam;
use App\Models\CustomizationPivotParam;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_authenticated_to_create_order()
    {
        $product = Product::factory()->create();
        
        $response = $this->post(route('products.order', $product->id), [
            'total_price' => 1000,
            'page_count' => 10,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_order_creation_requires_valid_data()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $response = $this->actingAs($user)->post(route('products.order', $product->id), [
            // Missing required total_price
            'page_count' => 10,
        ]);

        $response->assertSessionHasErrors(['total_price']);
    }

    public function test_order_creation_with_valid_data()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $response = $this->actingAs($user)->post(route('products.order', $product->id), [
            'total_price' => 1000,
            'page_count' => 10,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'total_price' => 1000,
            'page_count' => 10,
        ]);
    }

    public function test_required_customizations_validation()
    {
        $user = User::factory()->create();
        $mainCategory = MainCategory::factory()->create();
        $product = Product::factory()->create(['main_category_id' => $mainCategory->id]);
        
        // Create a required customization category
        $customizationCategory = CustomizationCategory::factory()->create([
            'title' => 'Ebat',
            'required' => true,
        ]);
        
        // Create a parameter for this category
        $param = CustomizationParam::factory()->create([
            'customization_category_id' => $customizationCategory->id,
            'key' => '30x50 Albüm',
        ]);
        
        // Link parameter to product
        CustomizationPivotParam::factory()->create([
            'product_id' => $product->id,
            'params_id' => $param->id,
        ]);
        
        $response = $this->actingAs($user)->post(route('products.order', $product->id), [
            'total_price' => 1000,
            'page_count' => 10,
            // Missing required customization
        ]);

        $response->assertSessionHasErrors(['customizations']);
    }

    public function test_order_creation_with_required_customizations()
    {
        $user = User::factory()->create();
        $mainCategory = MainCategory::factory()->create();
        $product = Product::factory()->create(['main_category_id' => $mainCategory->id]);
        
        // Create a required customization category
        $customizationCategory = CustomizationCategory::factory()->create([
            'title' => 'Ebat',
            'required' => true,
        ]);
        
        // Create a parameter for this category
        $param = CustomizationParam::factory()->create([
            'customization_category_id' => $customizationCategory->id,
            'key' => '30x50 Albüm',
        ]);
        
        // Link parameter to product
        CustomizationPivotParam::factory()->create([
            'product_id' => $product->id,
            'params_id' => $param->id,
        ]);
        
        $response = $this->actingAs($user)->post(route('products.order', $product->id), [
            'total_price' => 1000,
            'page_count' => 10,
            'customizations' => ['30x50 Albüm' => 'selected'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'total_price' => 1000,
            'page_count' => 10,
        ]);
    }
} 