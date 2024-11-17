<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return Inertia::render('Products/Index' , ['products' => $products]);
    } 

    public function create()
    {
        return Inertia::render('Products/Create');
    }

    public function store (Request $request) {
        // $product = 
    }
}
