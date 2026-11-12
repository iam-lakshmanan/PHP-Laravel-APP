<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetatagConfig extends Model
{
    use HasFactory;
    protected $table = 'metatag_config';
    protected $fillable = ['cid', 'instance', 'config'];

}
