<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldDataFieldAuthor extends Model
{
    use HasFactory;
    protected $table = 'field_data_field_author';

    protected $fillable = [
        'entity_type',
        'bundle',
        'deleted',
        'entity_id',
        'revision_id',
        'language',
        'delta',
        'field_active_course_value',
        'field_author_format'
    ];
}
