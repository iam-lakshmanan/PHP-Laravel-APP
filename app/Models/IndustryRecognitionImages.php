<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryRecognitionImages extends Model
{
    use HasFactory;
    protected $table = 'industry_recognition_images';
    protected $fillable = ['id', 'fid'];

}
