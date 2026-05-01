<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $table = 'vendor';

    protected $fillable = [
        'kode_vendor',
        'nama',
        'alamat',
        'no_telp',
        'email',
        'pic_nama',
        'pic_jabatan',
        'pic_no_telp',
        'termin_pembayaran',
        'metode_pengiriman',
        'catatan',
        'status',
    ];

    public function itemPrices()
    {
        return $this->hasMany(ItemVendor::class, 'vendor_id');
    }
}
