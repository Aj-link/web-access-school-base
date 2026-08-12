<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Request extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'request_type_id',
        'purpose',
        'status',
        'current_responsibility_center_id',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department():BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function requestType():BelongsTo
    {
        return $this->belongsTo(RequestType::class);
    }

    public function responsibilityCenter():BelongsTo
    {
        return $this->belongsTo(ResponsibilityCenter::class, 'current_responsibility_center_id');
    }
    public function items():HasMany
    {
        return $this->hasMany(RequestItem::class);
    }
}
