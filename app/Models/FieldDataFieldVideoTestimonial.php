<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldDataFieldVideoTestimonial extends Model
{
    use HasFactory;
    protected $table = 'field_data_field_video_testimonial';
    protected $fillable = ['entity_type', 'bundle', 'deleted', 'entity_id', 'revision_id', 'language', 'delta', 'field_video_testimonial_fid','field_video_testimonial_title','field_video_testimonial_data'];

}
