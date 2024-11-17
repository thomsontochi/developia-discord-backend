<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('status', 'active')
            ->paginate(12);

        return ProductResource::collection($products);
    }

    public function show($slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    public function byCategory($category_slug)
    {
        $products = Product::whereHas('category', function($query) use ($category_slug) {
            $query->where('slug', $category_slug);
        })
        ->where('status', 'active')
        ->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }
}