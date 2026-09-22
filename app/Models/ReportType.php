<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_name',
    ];

    public function reports():HasMany
    {
        return $this->hasMany(Report::class);
    }
}
