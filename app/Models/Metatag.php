<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metatag extends Model
{
    use HasFactory;
    protected $table = 'metatag';
    protected $fillable = ['entity_type', 'entity_id', 'revision_id','language','data'];

}
