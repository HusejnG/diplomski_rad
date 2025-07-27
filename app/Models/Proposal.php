<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_request_id',
        'designer_id',
        'title',
        'description',
        'total_price',
        'currency',
        'status',
    ];

    // Relacija sa zahtevom za ponudu
    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    // Relacija sa projektantom koji je kreirao ponudu
    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    // Relacija sa proizvodima (many-to-many)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'proposal_product')
                    ->withPivot('quantity', 'price_at_time_of_proposal')
                    ->withTimestamps();
    }
}
