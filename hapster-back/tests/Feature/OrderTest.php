<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderTest extends TestCase
{
    use RefreshDatabase; // resets DB before each test

    #[Test]
    public function it_can_get_all_orders()
    {
        // Create 3 orders with 1 item each
        Order::factory()
            ->count(3)
            ->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    #[Test]
    public function it_can_get_single_order()
    {

        $order = Order::factory()->create();

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
    }

    #[Test]
    public function it_can_create_an_order()
    {
        // Arrange: create a product in DB
        $product = Product::factory()->create();

        $data = [
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
        ];

        // Act: send POST request
        $response = $this->postJson('/api/orders', $data);

        // Assert: response status
        $response->assertStatus(201);

        // status depends on job queue , (async function)
        $status = $response->json('status');
        $this->assertTrue(
            in_array($status, ['pending', 'processing'])
        );
        $response->assertJsonPath('items.0.product_id', $product->id);
        $response->assertJsonPath('items.0.qty', 2);
        $this->assertEquals(
            (float) $response->json('items.0.price'),
            $product->price
        );
    }

}
