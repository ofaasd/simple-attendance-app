<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistribusiDetail extends Model
{
    use HasFactory;

    protected $table = 'distribusi_detail';

    protected $fillable = [
        'id_distribusi',
        'id_penerima_manfaat',
        'jml_kecil',
        'jml_besar',
        'jml_orcil',
        'jml_orbes_sekolah',
        'jml_bumil',
        'jml_busui',
        'jml_balita',
        'jml_orbes_b3',
        'jml_lainnya',
    ];

    public function distribusiMenu()
    {
        return $this->belongsTo(DistribusiMenu::class, 'id_distribusi');
    }

    public function penerimaManfaat()
    {
        return $this->belongsTo(PenerimaManfaat::class, 'id_penerima_manfaat');
    }
}

