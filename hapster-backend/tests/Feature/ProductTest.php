<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase; // resets DB for each test

    /** @test */

    public function it_can_get_all_products()
    {
        // Arrange: create some products
        Product::factory()->count(3)->create();

        // Act: make GET request to API
        $response = $this->getJson('/api/products');

        // Assert: check status & JSON structure
        $response->assertStatus(200)
            ->assertJsonCount(3); // exactly 3 products
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $data = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Product']);

        $this->assertDatabaseHas('products', $data);
    }
}
