<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldDataFieldIndustry extends Model
{
    use HasFactory;
    protected $table = 'field_data_field_industry';
    protected $fillable = [
        'entity_type',
        'bundle',
        'deleted',
        'entity_id',
        'revision_id',
        'language',
        'delta',
        'field_industry_tid'
    ];
}
