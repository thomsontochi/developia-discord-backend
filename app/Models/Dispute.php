<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'reason',
        'description',
        'status',
        'resolved_at',
        'resolution_notes',
    ];
}


