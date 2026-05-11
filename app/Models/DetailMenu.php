<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailMenu extends Model
{
    protected $table = 'detail_menu';

    protected $fillable = [
        'id_menu',
        'nama_detail_menu',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }
}
