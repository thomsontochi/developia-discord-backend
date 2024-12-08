<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

    public function edit(Category $category)
    {
        // dd('hello were coding live');
        return Inertia::render('Categories/Edit', ['category' => $category]);
    }
    
    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required', 
                    'string', 
                    'max:255', 
                    Rule::unique('categories', 'name')->ignore($category->id)
                ],
                'description' => 'nullable|string|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_url' => 'nullable|url'
            ]);

            // Update slug only if name changes
            if ($category->name !== $validated['name']) {
                $slug = Str::slug($validated['name']);
                $originalSlug = $slug;
                $count = 1;
                while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                $validated['slug'] = $slug;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image_path) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $category->image_path));
                }

                $path = $request->file('image')->store('categories', 'public');
                $validated['image_path'] = "/storage/$path";
                $validated['image_url'] = null; // Clear external URL if file uploaded
            }

            // Handle image URL
            if ($request->image_url) {
                // Clear existing uploaded image if URL is provided
                if ($category->image_path) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $category->image_path));
                }
                $validated['image_path'] = null;
                $validated['image_url'] = $request->image_url;
            }

            // Update the category
            $category->update($validated);

            return redirect()->route('categories.index')
                ->with('message', 'Category updated successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] === 1062) { // MySQL duplicate entry error code
                return back()->withErrors(['name' => 'A category with this name already exists.'])
                    ->withInput();
            }
            throw $e;
        }
    }
    public function destroy($category)
    {
        // find the category
        $category = Category::find($category);
       
        // Delete associated image if exists
        if ($category->image_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $category->image_path));
        }

        // Soft delete the category
        $category->delete();

            return redirect()->route('categories.index')
                ->with('message', 'Category deleted successfully');
        
    }




}
