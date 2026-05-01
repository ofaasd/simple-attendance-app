<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sppg extends Model
{
    use SoftDeletes;

    protected $table = 'sppg';

    protected $fillable = [
        'nama',
        'alamat',
        'location',
        'lat',
        'lng',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'sppg_user', 'sppg_id', 'user_id')->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'sppg_id');
    }

    public function kategori()
    {
        return $this->hasMany(Kategori::class, 'sppg_id');
    }

    public function uom()
    {
        return $this->hasMany(Uom::class, 'sppg_id');
    }
}
