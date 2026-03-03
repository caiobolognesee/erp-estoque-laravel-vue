<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create([
            'name' => $request->input('name'),
            'sale_price' => $request->input('sale_price'),
            'avg_cost' => 0,
            'stock' => 0,
        ]);

        return response()->json($product, 201);
    }

    public function index(): JsonResponse
    {
        return response()->json(Product::all());
    }
}
