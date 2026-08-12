<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestItem extends Model
{
    protected $fillable = [
        'request_id',
        'resource_id',
        'item_name',
        'quantity',
        'request_date',
        'start_time',
        'end_time',
    ];

    public function request():BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
    public function resource():BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
