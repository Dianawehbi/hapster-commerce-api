<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Jobs\ProcessOrderJob;
use App\Models\Product;
use Illuminate\Support\Facades\Request;

class OrderController extends Controller
{
    // GET /api/orders
    public function index()
    {
        return Order::with('items.product')->get();
    }

    // GET /api/orders/{id}
    public function show(Order $order)
    {
        return $order->load('items.product');
    }

    // POST /api/orders
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $order = Order::create(['status' => 'pending', 'total_price' => 0]);

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $order->items()->create([
                'product_id' => $product->id,
                'qty' => $item['qty'],
                'price' => $product->price,
            ]);
        }

        // dispatch job to process the order
        ProcessOrderJob::dispatch($order);

        return response()->json($order->load('items.product'), 201);
    }
}

