<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResourceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_name',
    ];

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

}
