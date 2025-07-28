<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'roof_type',
        'roof_area_sqm',
        'avg_monthly_consumption_kwh',
        'notes',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function proposal()
    {
        return $this->hasOne(Proposal::class);
    }
}