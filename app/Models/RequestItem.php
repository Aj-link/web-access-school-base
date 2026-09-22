<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestItem extends Model
{

    use HasFactory;

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
