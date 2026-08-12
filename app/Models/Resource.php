<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceFactory> */
    use HasFactory;

    protected $fillable = [
        'resource_type_id',
        'resource_name',
        'description',
        'quantity_available',
        'status',
    ];

public function allocations(): HasMany {
    return $this->hasMany(ResourceAllLocation::class);
}

public function usages(): HasMany {
    return $this->hasMany(ResourceUsage::class);
}

public function requestsItems(): HasMany {
    return $this->hasMany(RequestItem::class);

}

public function resourceType(): BelongsTo {
    return $this->belongsTo(ResourceType::class);


}
}
