<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestApproval extends Model
{
    protected $fillable = [
        'request_id',
        'approver_id',
        'approval_date',
        'status',
    ];

    public function request():BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function approver():BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
