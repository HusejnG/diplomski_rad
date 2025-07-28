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

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'proposal_product')
                    ->withPivot('quantity', 'price_at_time_of_proposal')
                    ->withTimestamps();
    }
}
