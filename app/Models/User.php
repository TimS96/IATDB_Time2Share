<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function items()
    {
        return $this->hasMany(\App\Models\Item::class);
    }

    public function reviewsReceived()
    {
        return $this->hasMany(\App\Models\Review::class, 'reviewee_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(\App\Models\Review::class, 'reviewer_id');
    }
}
