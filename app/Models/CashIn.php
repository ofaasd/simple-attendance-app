<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashIn extends Model
{
    use HasFactory;

    protected $table = 'cash_in';

    protected $fillable = [
        'sppg_id',
        'tanggal',
        'sumber_dana',
        'jumlah_dana',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_dana' => 'decimal:2',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }
}

