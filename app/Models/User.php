<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     

    // Relacije
    public function quoteRequests()
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function createdProposals() // Ponude koje je kreirao ovaj korisnik (projektant)
    {
        return $this->hasMany(Proposal::class, 'designer_id');
    }

    public function receivedProposals() // Ponude kreirane za zahteve ovog korisnika (kupac)
    {
        return $this->hasManyThrough(Proposal::class, QuoteRequest::class, 'user_id', 'quote_request_id');
    }


    // Helper metode za uloge
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
