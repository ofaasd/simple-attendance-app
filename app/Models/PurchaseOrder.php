<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFTED = 1;
    public const STATUS_REQUESTED = 2;
    public const STATUS_APPROVED_AKUNTAN = 3;
    public const STATUS_APPROVED_VERVAL = 4;
    public const STATUS_APPROVED_HEAD = 5;

    protected $table = 'purchase_order';

    protected $fillable = [
        'sppg_id',
        'kode_po',
        'tanggal_po',
        'tanggal_menu_dari',
        'tanggal_menu_sampai',
        'total_bayar',
        'status',
        'akuntan_comment',
        'verval_comment',
        'head_comment',
        'last_rejection_comment',
        'last_rejected_by_role',
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'tanggal_menu_dari' => 'date',
        'tanggal_menu_sampai' => 'date',
        'total_bayar' => 'decimal:2',
        'status' => 'integer',
    ];

    public function getLastRejectedByRoleLabelAttribute(): ?string
    {
        if (!$this->last_rejected_by_role) {
            return null;
        }

        return match ($this->last_rejected_by_role) {
            'akuntan' => 'Akuntan',
            'verval' => 'Verval',
            'head' => 'Head',
            default => ucfirst((string) $this->last_rejected_by_role),
        };
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFTED => 'Drafted',
            self::STATUS_REQUESTED => 'Requested',
            self::STATUS_APPROVED_AKUNTAN => 'Approved by Akuntan',
            self::STATUS_APPROVED_VERVAL => 'Approved by Verval',
            self::STATUS_APPROVED_HEAD => 'Approved by Head',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = self::statusLabels();

        return $labels[(int) $this->status] ?? 'Unknown';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_DRAFTED => 'badge-secondary',
            self::STATUS_REQUESTED => 'badge-warning',
            self::STATUS_APPROVED_AKUNTAN => 'badge-info',
            self::STATUS_APPROVED_VERVAL => 'badge-primary',
            self::STATUS_APPROVED_HEAD => 'badge-success',
            default => 'badge-dark',
        };
    }

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id');
    }
}
