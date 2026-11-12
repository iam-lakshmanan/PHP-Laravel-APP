<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldDataFieldLinks extends Model
{
    use HasFactory;
    protected $table='field_data_field_links';
    protected $fillable = ['entity_type','bundle','deleted','entity_id','revision_id','language','delta','field_links_url','field_links_title','field_links_attributes'];
}
