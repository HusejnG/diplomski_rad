<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Solarni fotonaponski panel iz internog kataloga opreme.
 */
class Panel extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturer',
        'model',
        'technology',
        'power_w',
        'efficiency_percent',
        'length_mm',
        'width_mm',
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

    /**
     * Površina jednog panela u m2 (na osnovu vanjskih dimenzija).
     */
    public function getAreaSqmAttribute(): float
    {
        return round(($this->length_mm * $this->width_mm) / 1_000_000, 3);
    }

    public function getLabelAttribute(): string
    {
        return "{$this->manufacturer} {$this->model} ({$this->power_w} W)";
    }
}
