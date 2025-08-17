<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

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
    public function show($id)
    {
        //  cache for 30 min
        return Cache::remember("products.$id", 1800, function () use ($id) {
            return Product::findOrFail($id);
        });
    }

    // POST /api/products
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $product = Product::create($data);
        Cache::forget('products.all'); // clear cache for list
        return response()->json($product, 201);
    }

    // PUT /api/products/{id}
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name' => 'string',
            'sku' => 'string|unique:products,sku,' . $id,
            'price' => 'numeric',
            'stock' => 'integer',
        ]);

        $product->update($data);
        Cache::forget('products.all');
        Cache::forget("products.$id");
        return response()->json($product);
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        Cache::forget('products.all');
        Cache::forget("products.$id");
        return response()->json(null, 204);
    }
}
