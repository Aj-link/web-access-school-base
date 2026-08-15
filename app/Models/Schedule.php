<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;
    protected $fillable = [
        'subject_code',
        'subject_name',
        'teacher',
        'section',
        'department',
        'year_level',
        'day_type',
        'start_time',
        'end_time',
        'room',
        'school_year',
        'semester',
    ];

    public function getDayLabelAttribute(): string
    {
        return match($this->day_type) {
            'MW'  => 'Monday & Wednesday',
            'TTH' => 'Tuesday & Thursday',
            'F'   => 'Friday',
            'SAT' => 'Saturday',
            default => $this->day_type,
        };
    }

    public function requests(): BelongsToMany
    {
        return $this->belongsToMany(Request::class, 'request_schedule');
    }
}
