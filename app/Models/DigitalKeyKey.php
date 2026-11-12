<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalKeyKey extends Model
{
    protected $table = 'digital_key_keys';
    protected $primaryKey = 'key_id';
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'key_hash',
        'label',
        'status',
        'expires',
        'max_uses',
        'use_count',
        'created',
        'uid',
    ];
}
