<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->paginate(9);
        return Inertia::render('Categories/Index', ['categories' => $categories]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_url' => 'nullable|url'
            ]);
    
            // Generate slug from name
            $slug = Str::slug($validated['name']);
            
            // Check for duplicate slug
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            
            $validated['slug'] = $slug;
    
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('categories', 'public');
                $validated['image_path'] = "/storage/$path";
            }
    
            if ($request->image_url) {
                $validated['image_url'] = $request->image_url;
            }
    
            Category::create($validated);
    
            return redirect()->route('categories.index')
                ->with('message', 'Category created successfully');
    
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] === 1062) { // MySQL duplicate entry error code
                return back()->withErrors(['name' => 'A category with this name already exists.'])
                            ->withInput();
            }
            throw $e;
        }
    }
}
