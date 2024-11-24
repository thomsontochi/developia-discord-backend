<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        // sort with latest first
        $products = Product::orderBy('created_at', 'desc')->paginate(6);
        return Inertia::render('Products/Index' , ['products' => $products]);
    } 


    public function create()
    {
        $categories = Category::all();
        return Inertia::render('Products/Create' , ['categories' => $categories]);
        
    }

    public function store(Request $request)
    {
       
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|url',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'status' => 'required|in:active,draft,archived',
            // 'slug' => 'required|string|unique:products,slug', 
        ]);

        try {
            Product::create($validated);
            return redirect()->route('products.index')
                ->with('message', 'Product created successfully');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to create product: ' . $e->getMessage()
            ]);
        }
    }

    public function edit (Product $product) {
        $categories = Category::all();

        return Inertia::render('Products/Edit' , [
        'product' => $product,
        'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|url',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'status' => 'required|in:active,draft,archived',
            // 'slug' => 'required|string|unique:products,slug,' . $product->id,
        ]);
    
        try {
            $product->update($validated);
            return redirect()->route('products.index')
                ->with('message', 'Product updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to update product'
            ]);
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('message', 'Product deleted successfully');
    }
}
