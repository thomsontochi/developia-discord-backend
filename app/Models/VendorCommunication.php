<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'type',
        'subject',
        'content',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }
}