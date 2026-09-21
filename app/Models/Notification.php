<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_id',
        'message',
        'type',
        'status',
    ];

public function user(): BelongsTo {
    return $this->belongsTo(User::class);
}

public function request(): BelongsTo
{
    return $this->belongsTo(Request::class);
}

}
