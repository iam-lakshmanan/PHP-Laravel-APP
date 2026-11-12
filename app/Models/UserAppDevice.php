<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAppDevice extends Model
{
    use HasFactory;
    protected $table = 'user_app_device';

    protected $fillable = [
        'user_id',
        'device_id',
        'fcm_token',
        'platform',
        'last_active_at'
    ];
}
