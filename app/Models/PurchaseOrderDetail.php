<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $table = 'purchase_order_detail';

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'custom_item_name',
        'custom_uom_id',
        'custom_item_satuan',
        'vendor_id',
        'qty',
        'qty_diterima',
        'harga',
        'harga_realisasi',
        'subtotal',
        'subtotal_realisasi',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'qty_diterima' => 'decimal:2',
        'harga' => 'decimal:2',
        'harga_realisasi' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'subtotal_realisasi' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function getItemNameAttribute()
    {
        if ($this->custom_item_name) {
            return $this->custom_item_name;
        }
        return $this->item?->nama ?? '-';
    }

    public function getItemSatuanAttribute()
    {
        if ($this->custom_uom_id) {
            return $this->customUom?->nama ?? ($this->custom_item_satuan ?? '-');
        }
        if ($this->custom_item_satuan) {
            return $this->custom_item_satuan;
        }
        return $this->item?->uom?->nama ?? '-';
    }

    public function customUom()
    {
        return $this->belongsTo(Uom::class, 'custom_uom_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}

