<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCustom extends Model
{
    use HasFactory;
    protected $table = 'menu_custom';
    protected $fillable = ['menu_name', 'title','description'];

}
