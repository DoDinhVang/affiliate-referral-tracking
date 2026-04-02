<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Shop extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'access_token',
        'scope',
        'installed_at',
    ];

    protected $casts = [
        'scope' => 'array',
        'installed_at' => 'datetime',
    ];
}
