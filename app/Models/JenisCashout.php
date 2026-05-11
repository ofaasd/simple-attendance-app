<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisCashout extends Model
{
    use HasFactory;

    protected $table = 'jenis_cashout';

    protected $fillable = [
        'nama',
    ];

    public function cashOuts()
    {
        return $this->hasMany(CashOut::class, 'jenis_cashout_id');
    }
}
