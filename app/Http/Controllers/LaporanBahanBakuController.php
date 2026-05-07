<?php

namespace App\Http\Controllers;

use App\Models\LaporanBahanBaku;
use App\Models\PurchaseOrderDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanBahanBakuController extends Controller
{
    private function ensureCanAccessReport(): void
    {
        if (!Auth::user()->hasAnyRole(['akuntan', 'verval', 'head'])) {
            abort(403, 'Anda tidak memiliki akses ke laporan bahan baku maker.');
        }
    }

    private function ensureAkuntanRole(): void
    {
        if (!Auth::user()->hasRole('akuntan')) {
            abort(403, 'Hanya role akuntan yang dapat mengelola laporan bahan baku maker.');
        }
    }

    private function scopeByBatch(LaporanBahanBaku $anchor)
    {
        $query = LaporanBahanBaku::query()
            ->where('nama_laporan', $anchor->nama_laporan)
            ->where('created_at', $anchor->created_at);

        if ($anchor->tanggal_laporan) {
            $query->whereDate('tanggal_laporan', $anchor->tanggal_laporan->toDateString());
        } else {
            $query->whereNull('tanggal_laporan');
        }

        if ($anchor->estimasi_tanggal_bayar) {
            $query->whereDate('estimasi_tanggal_bayar', $anchor->estimasi_tanggal_bayar->toDateString());
        } else {
            $query->whereNull('estimasi_tanggal_bayar');
        }

        return $query;
    }

    private function getReviewStageConfig(string $stage): array
    {
        return match ($stage) {
            'verval' => [
                'role' => 'verval',
                'expected_status' => LaporanBahanBaku::APPROVAL_REQUESTED,
                'approved_status' => LaporanBahanBaku::APPROVAL_APPROVED_VERVAL,
            ],
            'head' => [
                'role' => 'head',
                'expected_status' => LaporanBahanBaku::APPROVAL_APPROVED_VERVAL,
                'approved_status' => LaporanBahanBaku::APPROVAL_APPROVED_HEAD,
            ],
            default => throw new \InvalidArgumentException('Tahap review tidak valid.'),
        };
    }

    private function baseReceivedDetailQuery(): Builder
    {
        return PurchaseOrderDetail::query()
            ->join('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->join('item', 'item.id', '=', 'purchase_order_detail.item_id')
            ->join('vendor', 'vendor.id', '=', 'purchase_order_detail.vendor_id')
            ->leftJoin('uom', 'uom.id', '=', 'item.uom_id')
            ->whereNotNull('purchase_order_detail.item_id')
            ->whereNotNull('purchase_order_detail.vendor_id')
            ->whereNotNull('purchase_order_detail.qty_diterima')
            ->where('purchase_order_detail.qty_diterima', '>', 0)
            ->whereNotNull('purchase_order_detail.harga_realisasi')
            ->select([
                'purchase_order_detail.id as source_detail_id',
                'purchase_order.id as purchase_order_id',
                'purchase_order.tanggal_po as tanggal',
                'purchase_order.kode_po',
                'purchase_order_detail.item_id',
                'item.nama as item_nama',
                'uom.nama as uom_nama',
                'purchase_order_detail.vendor_id',
                'vendor.kode_vendor',
                'vendor.nama as vendor_nama',
                'purchase_order_detail.qty_diterima as volume',
                'purchase_order_detail.harga_realisasi as harga_satuan',
                DB::raw('COALESCE(purchase_order_detail.subtotal_realisasi, purchase_order_detail.qty_diterima * purchase_order_detail.harga_realisasi) as total'),
            ])
            ->orderBy('vendor.nama')
            ->orderBy('purchase_order.tanggal_po')
            ->orderBy('purchase_order_detail.id');
    }

    private function buildPreviewByVendor($rows, array $selectedDetailIds): array
    {
        $selectedMap = array_fill_keys($selectedDetailIds, true);

        $previewByVendor = $rows
            ->groupBy('vendor_id')
            ->map(function ($group) use ($selectedMap) {
                $first = $group->first();

                return [
                    'vendor_id' => $first->vendor_id,
                    'vendor_label' => trim(($first->kode_vendor ?? '') . ' ' . ($first->vendor_nama ?? '')),
                    'rows' => $group,
                    'vendor_total' => (float) $group->sum(function ($row) use ($selectedMap) {
                        return isset($selectedMap[(int) $row->source_detail_id]) ? (float) $row->total : 0;
                    }),
                ];
            })
            ->values();

        $grandTotal = (float) $rows->sum(function ($row) use ($selectedMap) {
            return isset($selectedMap[(int) $row->source_detail_id]) ? (float) $row->total : 0;
        });

        return [
            'previewByVendor' => $previewByVendor,
            'grandTotal' => $grandTotal,
        ];
    }

    public function index()
    {
        $this->ensureCanAccessReport();

        $reports = LaporanBahanBaku::query()
            ->select([
                DB::raw('MIN(id) as id'),
                'nama_laporan',
                'tanggal_laporan',
                'estimasi_tanggal_bayar',
                DB::raw('MIN(approval_status) as approval_status'),
                'created_at',
                DB::raw('COUNT(*) as jumlah_detail'),
                DB::raw('SUM(total) as grand_total'),
            ])
            ->groupBy('nama_laporan', 'tanggal_laporan', 'estimasi_tanggal_bayar', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        return view('laporan_bahan_baku.index', [
            'title' => 'Laporan Bahan Baku Maker',
            'reports' => $reports,
        ]);
    }

    public function create()
    {
        $this->ensureAkuntanRole();

        $usedDetailIds = LaporanBahanBaku::query()
            ->whereNotNull('purchase_order_detail_id')
            ->pluck('purchase_order_detail_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $availableRows = $this->baseReceivedDetailQuery()
            ->when(!empty($usedDetailIds), fn ($query) => $query->whereNotIn('purchase_order_detail.id', $usedDetailIds))
            ->get();

        $selectedDetailIds = collect(old('included_detail_ids', $availableRows->pluck('source_detail_id')->all()))
            ->map(fn ($detailId) => (int) $detailId)
            ->filter()
            ->values()
            ->all();

        $previewData = $this->buildPreviewByVendor($availableRows, $selectedDetailIds);

        return view('laporan_bahan_baku.create', [
            'title' => 'Generate Laporan Bahan Baku Maker',
            'previewByVendor' => $previewData['previewByVendor'],
            'grandTotal' => $previewData['grandTotal'],
            'today' => now()->toDateString(),
            'selectedDetailIds' => $selectedDetailIds,
            'availableCount' => $availableRows->count(),
        ]);
    }

    public function generate(Request $request)
    {
        $this->ensureAkuntanRole();

        $request->validate([
            'nama_laporan' => 'required|string|max:255',
            'tanggal_laporan' => 'required|date',
            'estimasi_tanggal_bayar' => 'required|date',
            'included_detail_ids' => 'required|array|min:1',
            'included_detail_ids.*' => 'integer',
        ]);

        $selectedDetailIds = collect($request->included_detail_ids)
            ->map(fn ($detailId) => (int) $detailId)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $usedDetailIds = LaporanBahanBaku::query()
            ->whereNotNull('purchase_order_detail_id')
            ->pluck('purchase_order_detail_id')
            ->map(fn ($detailId) => (int) $detailId)
            ->values()
            ->all();

        $sourceRows = $this->baseReceivedDetailQuery()
            ->whereIn('purchase_order_detail.id', $selectedDetailIds)
            ->when(!empty($usedDetailIds), fn ($query) => $query->whereNotIn('purchase_order_detail.id', $usedDetailIds))
            ->get();

        if ($sourceRows->count() !== count($selectedDetailIds)) {
            return back()
                ->withInput()
                ->with('error', 'Sebagian PO receive sudah dipakai laporan lain. Silakan refresh halaman dan pilih ulang data yang tersedia.');
        }

        $vendorPaymentMap = $sourceRows
            ->groupBy('vendor_id')
            ->map(fn ($group) => (float) $group->sum('total'))
            ->all();

        DB::transaction(function () use ($sourceRows, $vendorPaymentMap, $request) {
            if ($sourceRows->isEmpty()) {
                return;
            }

            $now = now();
            $insertPayload = $sourceRows->map(function ($row) use ($vendorPaymentMap, $request, $now) {
                $vendorId = (int) $row->vendor_id;

                return [
                    'nama_laporan' => $request->nama_laporan,
                    'tanggal_laporan' => $request->tanggal_laporan,
                    'estimasi_tanggal_bayar' => $request->estimasi_tanggal_bayar,
                    'approval_status' => LaporanBahanBaku::APPROVAL_DRAFT,
                    'purchase_order_detail_id' => (int) $row->source_detail_id,
                    'tanggal' => $row->tanggal,
                    'item_id' => (int) $row->item_id,
                    'volume' => (float) $row->volume,
                    'harga_satuan' => (float) $row->harga_satuan,
                    'total' => (float) $row->total,
                    'total_pembayaran' => (float) ($vendorPaymentMap[$vendorId] ?? 0),
                    'vendor_id' => $vendorId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->all();

            LaporanBahanBaku::query()->insert($insertPayload);
        });

        return redirect()
            ->route('laporan_bahan_baku.index')
            ->with('success', 'Laporan bahan baku berhasil di-generate dari data receive terpilih.');
    }

    public function edit(int $id)
    {
        $this->ensureAkuntanRole();

        $reportAnchor = LaporanBahanBaku::findOrFail($id);

        if ((int) $reportAnchor->approval_status !== LaporanBahanBaku::APPROVAL_DRAFT) {
            return redirect()
                ->route('laporan_bahan_baku.index')
                ->with('error', 'Laporan yang sudah direquest/disetujui tidak dapat diedit.');
        }

        $currentRows = $this->scopeByBatch($reportAnchor)
            ->orderBy('id')
            ->get();

        $currentSelectedIds = $currentRows
            ->pluck('purchase_order_detail_id')
            ->filter()
            ->map(fn ($detailId) => (int) $detailId)
            ->values()
            ->all();

        $usedByOtherIds = LaporanBahanBaku::query()
            ->whereNotNull('purchase_order_detail_id')
            ->whereNotIn('id', $currentRows->pluck('id'))
            ->pluck('purchase_order_detail_id')
            ->map(fn ($detailId) => (int) $detailId)
            ->values()
            ->all();

        $availableRows = $this->baseReceivedDetailQuery()
            ->when(!empty($usedByOtherIds), function ($query) use ($usedByOtherIds, $currentSelectedIds) {
                $query->where(function ($q) use ($usedByOtherIds, $currentSelectedIds) {
                    $q->whereNotIn('purchase_order_detail.id', $usedByOtherIds);

                    if (!empty($currentSelectedIds)) {
                        $q->orWhereIn('purchase_order_detail.id', $currentSelectedIds);
                    }
                });
            })
            ->get();

        $selectedDetailIds = collect(old('included_detail_ids', $currentSelectedIds))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        if (empty($selectedDetailIds)) {
            $selectedDetailIds = $availableRows
                ->pluck('source_detail_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        $previewData = $this->buildPreviewByVendor($availableRows, $selectedDetailIds);

        return view('laporan_bahan_baku.edit', [
            'title' => 'Edit Laporan Bahan Baku Maker',
            'reportAnchor' => $reportAnchor,
            'previewByVendor' => $previewData['previewByVendor'],
            'grandTotal' => $previewData['grandTotal'],
            'selectedDetailIds' => $selectedDetailIds,
            'availableCount' => $availableRows->count(),
            'currentGrandTotal' => (float) $currentRows->sum('total'),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->ensureAkuntanRole();

        $request->validate([
            'nama_laporan' => 'required|string|max:255',
            'tanggal_laporan' => 'required|date',
            'estimasi_tanggal_bayar' => 'required|date',
            'included_detail_ids' => 'required|array|min:1',
            'included_detail_ids.*' => 'integer',
        ]);

        $reportAnchor = LaporanBahanBaku::findOrFail($id);

        if ((int) $reportAnchor->approval_status !== LaporanBahanBaku::APPROVAL_DRAFT) {
            return redirect()
                ->route('laporan_bahan_baku.index')
                ->with('error', 'Laporan yang sudah direquest/disetujui tidak dapat diedit.');
        }

        $currentRows = $this->scopeByBatch($reportAnchor)->get();
        $currentSelectedIds = $currentRows
            ->pluck('purchase_order_detail_id')
            ->filter()
            ->map(fn ($detailId) => (int) $detailId)
            ->values()
            ->all();

        $usedByOtherIds = LaporanBahanBaku::query()
            ->whereNotNull('purchase_order_detail_id')
            ->whereNotIn('id', $currentRows->pluck('id'))
            ->pluck('purchase_order_detail_id')
            ->map(fn ($detailId) => (int) $detailId)
            ->values()
            ->all();

        $selectedDetailIds = collect($request->included_detail_ids)
            ->map(fn ($detailId) => (int) $detailId)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $sourceRows = $this->baseReceivedDetailQuery()
            ->whereIn('purchase_order_detail.id', $selectedDetailIds)
            ->when(!empty($usedByOtherIds), function ($query) use ($usedByOtherIds, $currentSelectedIds) {
                $query->where(function ($q) use ($usedByOtherIds, $currentSelectedIds) {
                    $q->whereNotIn('purchase_order_detail.id', $usedByOtherIds);

                    if (!empty($currentSelectedIds)) {
                        $q->orWhereIn('purchase_order_detail.id', $currentSelectedIds);
                    }
                });
            })
            ->get();

        if ($sourceRows->count() !== count($selectedDetailIds)) {
            return back()
                ->withInput()
                ->with('error', 'Sebagian PO receive sudah diambil oleh laporan lain. Silakan refresh dan pilih ulang data yang tersedia.');
        }

        $vendorPaymentMap = $sourceRows
            ->groupBy('vendor_id')
            ->map(fn ($group) => (float) $group->sum('total'))
            ->all();

        DB::transaction(function () use ($reportAnchor, $sourceRows, $vendorPaymentMap, $request) {
            $createdAt = $reportAnchor->created_at;

            $this->scopeByBatch($reportAnchor)->delete();

            $now = now();
            $insertPayload = $sourceRows->map(function ($row) use ($vendorPaymentMap, $request, $createdAt, $now) {
                $vendorId = (int) $row->vendor_id;

                return [
                    'nama_laporan' => $request->nama_laporan,
                    'tanggal_laporan' => $request->tanggal_laporan,
                    'estimasi_tanggal_bayar' => $request->estimasi_tanggal_bayar,
                    'approval_status' => LaporanBahanBaku::APPROVAL_DRAFT,
                    'purchase_order_detail_id' => (int) $row->source_detail_id,
                    'tanggal' => $row->tanggal,
                    'item_id' => (int) $row->item_id,
                    'volume' => (float) $row->volume,
                    'harga_satuan' => (float) $row->harga_satuan,
                    'total' => (float) $row->total,
                    'total_pembayaran' => (float) ($vendorPaymentMap[$vendorId] ?? 0),
                    'vendor_id' => $vendorId,
                    'created_at' => $createdAt,
                    'updated_at' => $now,
                ];
            })->all();

            LaporanBahanBaku::query()->insert($insertPayload);
        });

        return redirect()
            ->route('laporan_bahan_baku.index')
            ->with('success', 'Laporan bahan baku berhasil diperbarui.');
    }

    public function requestApproval(int $id)
    {
        $this->ensureAkuntanRole();

        $reportAnchor = LaporanBahanBaku::findOrFail($id);

        if ((int) $reportAnchor->approval_status !== LaporanBahanBaku::APPROVAL_DRAFT) {
            return back()->with('error', 'Laporan hanya bisa direquest saat status Draft.');
        }

        $this->scopeByBatch($reportAnchor)->update([
            'approval_status' => LaporanBahanBaku::APPROVAL_REQUESTED,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Request approval berhasil dikirim ke verval.');
    }

    public function review(Request $request, int $id, string $stage)
    {
        $config = $this->getReviewStageConfig($stage);

        if (!Auth::user()->hasRole($config['role'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk review tahap ini.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $reportAnchor = LaporanBahanBaku::findOrFail($id);

        if ((int) $reportAnchor->approval_status !== (int) $config['expected_status']) {
            return back()->with('error', 'Status laporan tidak sesuai untuk tahap review ini.');
        }

        $nextStatus = $request->action === 'approve'
            ? (int) $config['approved_status']
            : LaporanBahanBaku::APPROVAL_DRAFT;

        $this->scopeByBatch($reportAnchor)->update([
            'approval_status' => $nextStatus,
            'updated_at' => now(),
        ]);

        if ($request->action === 'approve') {
            if ($stage === 'verval') {
                return back()->with('success', 'Laporan disetujui verval dan lanjut ke approval head.');
            }

            return back()->with('success', 'Laporan disetujui head.');
        }

        return back()->with('success', 'Laporan ditolak dan dikembalikan ke Draft. Akuntan harus request ulang.');
    }

    public function destroy(int $id)
    {
        $this->ensureAkuntanRole();

        $reportAnchor = LaporanBahanBaku::findOrFail($id);

        if ((int) $reportAnchor->approval_status !== LaporanBahanBaku::APPROVAL_DRAFT) {
            return back()->with('error', 'Laporan hanya bisa dihapus saat status Draft.');
        }

        $this->scopeByBatch($reportAnchor)->delete();

        return redirect()
            ->route('laporan_bahan_baku.index')
            ->with('success', 'Laporan bahan baku berhasil dihapus.');
    }
}
