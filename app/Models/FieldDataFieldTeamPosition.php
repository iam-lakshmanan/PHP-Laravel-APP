<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldDataFieldTeamPosition extends Model
{
    use HasFactory;
    protected $table='field_data_field_team_position';
    protected $fillable =['entity_type','bundle','deleted','entity_id','revision_id','language','delta','field_team_position_value','field_team_position_format'];
}
