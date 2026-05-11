<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenerimaManfaat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penerima_manfaat';

    protected $fillable = [
        'nama',
        'kategori',
        'alamat',
        'no_telp',
        'pic',
    ];
    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }
    public function distribusiDetails()
    {
        return $this->hasMany(DistribusiDetail::class, 'id_penerima_manfaat');
    }
}

