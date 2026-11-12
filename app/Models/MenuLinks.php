<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuLinks extends Model
{
    use HasFactory;
    protected $table = 'menu_links';
    protected $fillable = ['menu_name', 'mlid','plid','link_path','router_path','link_title','options','module','hidden','external','has_children','expanded','weight','depth','customized','p1','p2','p3','p4','p5','p6','p7','p8','p9','updated'];

    public function subMenu()
    {
        return $this->hasMany(MenuLinks::class, 'plid', 'mlid')
            ->where(function ($query) {
                $query->where('p2', '!=', 0)->where('hidden', 0)
                    ->orWhere('p3', '!=', 0)
                    ->orWhere('p4', '!=', 0)
                    ->orWhere('p5', '!=', 0)
                    ->orWhere('p6', '!=', 0)
                    ->orWhere('p7', '!=', 0)
                    ->orWhere('p8', '!=', 0)
                    ->orWhere('p9', '!=', 0);
            })
            ->with('megaMenu');
    }

    public function megaMenu()
    {
        return $this->hasMany(MenuLinks::class, 'plid', 'mlid')->where('hidden', 0);
    }

}
