<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;

    protected $fillable = [
        'department_name',
    ];
public function users(): HasMany {
    return $this->hasMany(User::class);
}

public function resourcesAllLocation(): HasMany {
    return $this->hasMany(ResourceAllLocation::class);

}

public function requests(): HasMany {
    return $this->hasMany(Request::class);
}


}
