<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalKeyUserLogin extends Model
{
    protected $table = 'digital_key_user_logins';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'key_id',
        'first_login',
        'first_login_at',
        'login_status',
    ];

    protected $casts = [
        'first_login' => 'boolean',
        'first_login_at' => 'datetime',
    ];
}
