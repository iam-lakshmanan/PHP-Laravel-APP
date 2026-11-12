<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalKeyClient extends Model
{
    protected $table = 'digital_key_clients';

    protected $primaryKey = 'client_id';

    // The existing table uses created and changed,
    // not Laravel's default created_at and updated_at.
    public $timestamps = false;

    protected $fillable = [
        'name',
        'request_date',
        'number_of_keys',
        'printable',
        'status',
    ];
}
