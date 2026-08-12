<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceAllLocation extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceAllLocationFactory> */
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'department_id',
        'allocated_quantity',
    ];

public function resource(): BelongsTo {
    return $this->belongsTo(Resource::class);
}

public function department(): BelongsTo {
    return $this->belongsTo(Department::class);

}
}
