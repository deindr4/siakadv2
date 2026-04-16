<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // 👈 tambahkan ini

class User extends Authenticatable
{
    use Notifiable, HasRoles; // 👈 tambahkan HasRoles di sini

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_default_password',
        'default_password_hint',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
