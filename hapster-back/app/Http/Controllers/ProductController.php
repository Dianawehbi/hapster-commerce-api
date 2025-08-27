<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    public function index()
    {
        // 300 second = 5min
        return Cache::remember('products.all', 300, function () {
            return Product::all();
        });
    }

    // GET /api/products/{id}
    public function show(Product $product)
    {
        //  cache for 30 min
        return Cache::remember("products.{$product->id}", now()->addMinutes(30), function () use ($product) {
            return response()->json($product, 200);
        });
    }

    // POST /api/products
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($data);

        Cache::forget('products.all');
        return response()->json($product, 201);
    }

    // PUT /api/products/{id}
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product->update($data);

        Cache::forget('products.all');
        Cache::forget("products.{$product->id}");

        return response()->json($product);
    }

    // DELETE /api/products/{id}
    public function destroy(Product $product)
    {
        $product->delete();
        Cache::forget('products.all');
        Cache::forget("products.{$product->id}");
        return response()->json(null, 204);
    }

}
