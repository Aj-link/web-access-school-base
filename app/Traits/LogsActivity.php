<?php

namespace App\Traits;  // Make sure this is App\Traits

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logActivity('created', $model);
        });

        static::updated(function ($model) {
            self::logActivity('updated', $model);
        });

        static::deleted(function ($model) {
            self::logActivity('deleted', $model);
        });
    }

    protected static function logActivity($action, $model)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'table_name' => $model->getTable(),
                'record' => json_encode([
                    'id' => $model->id,
                    'data' => $model->getAttributes()
                ]),
            ]);
        }
    }
}
