<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBahanBaku extends Model
{
    public const APPROVAL_DRAFT = 0;
    public const APPROVAL_REQUESTED = 1;
    public const APPROVAL_APPROVED_VERVAL = 2;
    public const APPROVAL_APPROVED_HEAD = 3;

    protected $table = 'laporan_bahan_baku';

    protected $fillable = [
        'nama_laporan',
        'tanggal_laporan',
        'estimasi_tanggal_bayar',
        'approval_status',
        'purchase_order_detail_id',
        'tanggal',
        'item_id',
        'volume',
        'harga_satuan',
        'total',
        'total_pembayaran',
        'vendor_id',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'estimasi_tanggal_bayar' => 'date',
        'approval_status' => 'integer',
        'tanggal' => 'date',
        'volume' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total' => 'decimal:2',
        'total_pembayaran' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function purchaseOrderDetail()
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }

    public static function approvalLabels(): array
    {
        return [
            self::APPROVAL_DRAFT => 'Draft',
            self::APPROVAL_REQUESTED => 'Requested',
            self::APPROVAL_APPROVED_VERVAL => 'Approved by Verval',
            self::APPROVAL_APPROVED_HEAD => 'Approved by Head',
        ];
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        $labels = self::approvalLabels();

        return $labels[(int) $this->approval_status] ?? 'Unknown';
    }

    public function getApprovalStatusBadgeClassAttribute(): string
    {
        return match ((int) $this->approval_status) {
            self::APPROVAL_DRAFT => 'badge-secondary',
            self::APPROVAL_REQUESTED => 'badge-warning',
            self::APPROVAL_APPROVED_VERVAL => 'badge-info',
            self::APPROVAL_APPROVED_HEAD => 'badge-success',
            default => 'badge-dark',
        };
    }
}
