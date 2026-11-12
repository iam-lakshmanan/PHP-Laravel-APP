<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldDataFieldAreas extends Model
{
    use HasFactory;
    protected $table = 'field_data_field_areas';
    protected $fillable = [
        'entity_type',
        'bundle',
        'deleted',
        'entity_id',
        'revision_id',
        'language',
        'delta',
        'field_areas_tid'
    ];
}
