<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceUsage extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceUsageFactory> */
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'user_id',
        'quantity_used',
        'used_date',
    ];

public function resource(): BelongsTo {
    return $this->belongsTo(Resource::class);
}

public function user(): BelongsTo {
    return $this->belongsTo(User::class);
}

}
