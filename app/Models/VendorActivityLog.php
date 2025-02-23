<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'action',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
