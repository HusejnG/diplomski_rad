<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     

    public function quoteRequests()
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function createdProposals() 
    {
        return $this->hasMany(Proposal::class, 'designer_id');
    }

    public function receivedProposals()
    {
        return $this->hasManyThrough(Proposal::class, QuoteRequest::class, 'user_id', 'quote_request_id');
    }


    public function isAdmin()
    {
        return $this->role === 'admin';
    }

     public function isDesigner()
    {
        return $this->role === 'designer';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}
