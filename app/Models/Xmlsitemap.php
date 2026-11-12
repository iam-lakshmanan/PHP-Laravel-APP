<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Xmlsitemap extends Model
{
    use HasFactory;
    protected $table = 'xmlsitemap';
    protected $fillable = ['id','type','subtype','loc','language','access','status','status_override','lastmod','priority','priority_override','changefreq','changecount'];
}
