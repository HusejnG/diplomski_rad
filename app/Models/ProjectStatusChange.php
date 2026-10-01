<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Jedan zapis u historiji statusa solarnog projekta.
 */
class ProjectStatusChange extends Model
{
    protected $fillable = [
        'solar_project_id',
        'from_status',
        'to_status',
        'user_id',
        'note',
    ];

    public function project()
    {
        return $this->belongsTo(SolarProject::class, 'solar_project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getToStatusLabelAttribute(): string
    {
        return SolarProject::STATUS_LABELS[$this->to_status] ?? $this->to_status;
    }
}
