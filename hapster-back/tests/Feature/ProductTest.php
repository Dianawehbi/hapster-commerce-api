<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductTest extends TestCase
{
    use RefreshDatabase; // resets DB for each test

    #[Test]
    public function it_can_list_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    #[Test]
    public function it_can_show_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id
            ]);

    }

    #[Test]
    public function it_can_store_a_product()
    {
        $data = [
            'name' => 'Test Product',
            'sku' => 'SKU-000',
            'price' => 99.99,
            'stock' => 10,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonPath('name', 'Test Product');

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    #[Test]
    public function it_can_update_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'price' => 150,
            'stock' => 20,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('name', 'Updated Product');

        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    #[Test]
    public function it_can_delete_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

}
