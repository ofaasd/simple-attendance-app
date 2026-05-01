<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uom extends Model
{
    use SoftDeletes;

    protected $table = 'uom';

    protected $fillable = [
        'sppg_id',
        'nama',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'uom_id');
    }
}
