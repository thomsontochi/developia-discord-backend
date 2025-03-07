<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Dispute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'vendor_id',
        'status',
        'total_amount',
        'dispute_flag',
        'billing_address',
        'payment_status',
        'payment_method',
        'notes'
    ];

    // Relationship with customer (User model)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Relationship with vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Relationship with order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship with disputes
    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    // Relationship with payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
  
}
