<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistribusiMenu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'distribusi_menu';

    protected $fillable = [
        'id_menu',
        'tanggal_pengiriman',
        'tanggal_diterima',
        'foto_menu',
        'foto_suhu',
        'jumlah',
        'status',
    ];

    protected $casts = [
        'tanggal_pengiriman' => 'datetime',
        'tanggal_diterima' => 'datetime',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    public function distribusiDetails()
    {
        return $this->hasMany(DistribusiDetail::class, 'id_distribusi');
    }
}
