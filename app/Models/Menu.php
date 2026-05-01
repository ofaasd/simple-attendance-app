<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menu';

    protected $fillable = [
        'tanggal',
        'sppg_id',
        'nama',
        'kategori_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'menu_item', 'menu_id', 'item_id')->withTimestamps();
    }

    public function detailMenus()
    {
        return $this->hasMany(DetailMenu::class, 'id_menu');
    }
}
