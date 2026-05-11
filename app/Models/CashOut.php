<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashOut extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cash_out';

    protected $fillable = [
        'jenis_cashout_id',
        'sppg_id',
        'nominal',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function jenisCashout()
    {
        return $this->belongsTo(JenisCashout::class, 'jenis_cashout_id');
    }

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }
}
