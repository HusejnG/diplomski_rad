<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Invertor iz internog kataloga opreme.
 */
class Inverter extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturer',
        'model',
        'type',
        'rated_power_kw',
        'max_pv_power_kw',
        'phases',
        'price_bam',
        'warranty_years',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLabelAttribute(): string
    {
        return "{$this->manufacturer} {$this->model} ({$this->rated_power_kw} kW)";
    }
}
