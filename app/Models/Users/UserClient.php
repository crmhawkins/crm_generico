<?php

namespace App\Models\Users;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserClient extends Authenticatable
{
    protected $table = 'users'; // Apunta a la tabla `users`

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
        'password' => 'hashed', // Usa el hashing de contraseñas de Laravel
    ];
}

