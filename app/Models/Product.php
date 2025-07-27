<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'manufacturer',
        'model',
        'description',
        'price',
        'currency',
        'power_w',
        'length_mm',
        'width_mm',
        'height_mm',
        'image_path',
    ];


    // Relacija sa ponudama (many-to-many)
    public function proposals()
    {
        return $this->belongsToMany(Proposal::class, 'proposal_product')
                    ->withPivot('quantity', 'price_at_time_of_proposal')
                    ->withTimestamps();
    }
}