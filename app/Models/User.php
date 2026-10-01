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

    /**
     * Solarni projekti koje je korisnik sam kreirao (uloga: korisnik/kupac).
     */
    public function solarProjects()
    {
        return $this->hasMany(SolarProject::class, 'user_id');
    }

    /**
     * Projekti koje je ovaj projektant preuzeo na obradu (uloga: projektant).
     */
    public function assignedSolarProjects()
    {
        return $this->hasMany(SolarProject::class, 'designer_id');
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
