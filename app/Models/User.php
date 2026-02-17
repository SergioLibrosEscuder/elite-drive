<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Campos de tu base de datos
    protected $fillable = [
        'dni',
        'first_name',
        'last_name',
        'second_last_name',
        'birth_date',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'email_verified_at',
    ];

    // Ensure password and token are hidden fields
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Hash password
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
