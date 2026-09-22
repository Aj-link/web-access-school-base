<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_name',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
