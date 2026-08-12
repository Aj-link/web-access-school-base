<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceType extends Model
{
    protected $fillable = [
        'type_name',
    ];

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

}
