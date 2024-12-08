<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'store_name',
        'store_description',
        'business_category',
        'address',
        'business_hours',
        'payment_details',
        'store_logo',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'payment_details' => 'array',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Query Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // Accessor for full store details
    public function getStoreDetailsAttribute()
    {
        return [
            'name' => $this->store_name,
            'description' => $this->store_description,
            'logo' => $this->store_logo,
            'business_hours' => $this->business_hours,
            'address' => $this->address,
        ];
    }
}
