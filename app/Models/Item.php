<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $table = 'item';

    protected $fillable = [
        'sppg_id',
        'nama',
        'kategori_id',
        'uom_id',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_item', 'item_id', 'menu_id')->withTimestamps();
    }

    public function vendorPrices()
    {
        return $this->hasMany(ItemVendor::class, 'item_id');
    }
}

