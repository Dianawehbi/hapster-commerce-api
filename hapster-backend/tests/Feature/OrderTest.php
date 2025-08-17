<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase; // resets DB before each test

    /** @test */
    public function it_can_get_all_orders()
    {
        $product = Product::factory()->create();

        // Create 3 orders with 1 item each
        Order::factory()
            ->count(3)
            ->hasItems(1, ['product_id' => $product->id, 'qty' => 1, 'price' => $product->price])
            ->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_get_single_order()
    {
        $product = Product::factory()->create();

        $order = Order::factory()
            ->hasItems(1, ['product_id' => $product->id, 'qty' => 2, 'price' => $product->price])
            ->create();

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $order->id,
                'product_id' => $product->id,
            ]);
    }

    /** @test */
    public function it_can_create_an_order()
    {
        $product = Product::factory()->create();

        $data = [
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
        ];

        $response = $this->postJson('/api/orders', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'product_id' => $product->id,
                'qty' => 2,
            ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'qty' => 2,
        ]);
    }
}
