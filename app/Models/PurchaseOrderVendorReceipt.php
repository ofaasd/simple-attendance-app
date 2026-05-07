<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderVendorReceipt extends Model
{
    protected $table = 'purchase_order_vendor_receipt';

    protected $fillable = [
        'purchase_order_id',
        'vendor_id',
        'nota_path',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
