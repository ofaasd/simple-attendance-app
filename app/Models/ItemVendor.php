<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemVendor extends Model
{
    use SoftDeletes;

    protected $table = 'item_vendor';

    protected $fillable = [
        'sppg_id',
        'item_id',
        'vendor_id',
        'tanggal',
        'harga',
        'rank',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'harga' => 'decimal:2',
        'rank' => 'integer',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
