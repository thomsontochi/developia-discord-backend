<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
        'sizes',
        'colors',
        'category_id',
        'slug',
        'status',
    ];

    protected $casts = [
        'sizes' => 'array',
        'colors' => 'array',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
   
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name);
                
                // Ensure unique slug
                $count = 2;
                while (static::where('slug', $product->slug)->exists()) {
                    $product->slug = Str::slug($product->name) . '-' . $count;
                    $count++;
                }
            }
        });

        // Add this for updating
        static::updating(function ($product) {
            if ($product->isDirty('name')) {  // Only update slug if name has changed
                $product->slug = Str::slug($product->name);
                
                // Ensure unique slug (excluding current product)
                $count = 2;
                while (static::where('slug', $product->slug)
                            ->where('id', '!=', $product->id)
                            ->exists()) {
                    $product->slug = Str::slug($product->name) . '-' . $count;
                    $count++;
                }
            }
        });
    }




}
