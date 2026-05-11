<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;

class SppgUser extends Model
{
    protected $table = 'sppg_user';

    protected $fillable = [
        'user_id',
        'sppg_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }
}

