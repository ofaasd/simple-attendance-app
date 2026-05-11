<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Item;
use App\Models\ItemVendor;
use App\Models\Menu;
use App\Models\PurchaseOrder;
use App\Models\Sppg;
use App\Models\Uom;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    private const EMPLOYEE_ROLE_ID = 1;
    private const STATUS_DRAFT = 0;
    private const STATUS_RECEIVED = 1;
    private const STATUS_APPROVED_AKUNTAN = 2;
    private const STATUS_APPROVED_VERVAL = 3;
    private const STATUS_APPROVED_HEAD = 4;

    private function getSppgOptions()
    {
        $isEmployee = Auth::user()->hasRole\('perwakilan\ yayasan'\);
        $isApprover = Auth::user()->hasAnyRole(['akuntan', 'verval', 'head']);

        if ($isEmployee) {
            $sppg = Sppg::where('user_id', Auth::id())->orderBy('nama')->get();
        } elseif ($isApprover) {
            $sppg = Auth::user()->sppgs()->orderBy('nama')->get();
        } else {
            $sppg = Sppg::orderBy('nama')->get();
        }

        return compact('isEmployee', 'sppg');
    }

    private function ensureApproverCanAccessSppg(PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->hasAnyRole(['akuntan', 'verval', 'head'])) {
            return null;
        }

        $hasAccess = Auth::user()->sppgs()
            ->where('sppg.id', (int) $purchaseOrder->sppg_id)
            ->exists();

        if ($hasAccess) {
            return null;
        }

        abort(403, 'Anda tidak memiliki akses review pada SPPG ini.');
    }

    private function loadPurchaseOrderForDocument(PurchaseOrder $purchaseOrder): PurchaseOrder
    {
        return tap($purchaseOrder)->load([
            'sppg:id,nama,alamat',
            'details.item:id,nama,uom_id',
            'details.item.uom:id,nama',
            'details.customUom:id,nama',
            'details.vendor:id,kode_vendor,nama,alamat,metode_pengiriman',
            'vendorReceipts:id,purchase_order_id,vendor_id,nota_path',
        ]);
    }

    private function buildVendorDocumentGroups(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->loadMissing('vendorReceipts');
        $receiptsByVendorId = $purchaseOrder->vendorReceipts->keyBy(function ($receipt) {
            return (int) $receipt->vendor_id;
        });

        return $purchaseOrder->details
            ->groupBy(function ($detail) {
                return (int) ($detail->vendor_id ?? 0);
            })
            ->map(function ($details) use ($receiptsByVendorId) {
                $vendor = optional($details->first())->vendor;
                $receipt = $receiptsByVendorId->get((int) optional($vendor)->id);

                return [
                    'vendor' => $vendor,
                    'vendor_label' => trim(($vendor->kode_vendor ?? '') . ' ' . ($vendor->nama ?? '')),
                    'details' => $details->values(),
                    'total' => (float) $details->sum('subtotal'),
                    'receipt' => $receipt,
                ];
            })
            ->sortBy(function ($group) {
                return strtolower($group['vendor_label'] ?: '-');
            })
            ->values();
    }

    private function getReviewStageConfig(string $stage): array
    {
        return match ($stage) {
            'akuntan' => [
                'role' => 'akuntan',
                'expected_status' => self::STATUS_RECEIVED,
                'approved_status' => self::STATUS_APPROVED_AKUNTAN,
                'comment_field' => 'akuntan_comment',
            ],
            'verval' => [
                'role' => 'verval',
                'expected_status' => self::STATUS_APPROVED_AKUNTAN,
                'approved_status' => self::STATUS_APPROVED_VERVAL,
                'comment_field' => 'verval_comment',
            ],
            'head' => [
                'role' => 'head',
                'expected_status' => self::STATUS_APPROVED_VERVAL,
                'approved_status' => self::STATUS_APPROVED_HEAD,
                'comment_field' => 'head_comment',
            ],
            default => throw new \InvalidArgumentException('Tahap review tidak valid.'),
        };
    }

    private function ensureEmployeeCanAccessSppg(int $sppgId): ?\Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            return null;
        }

        $ownedSppg = Sppg::where('id', $sppgId)->where('user_id', Auth::id())->exists();
        if ($ownedSppg) {
            return null;
        }

        return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
    }

    private function ensureEmployeeCanAccessSppgForView(int $sppgId)
    {
        if (!Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            return null;
        }

        $ownedSppg = Sppg::where('id', $sppgId)->where('user_id', Auth::id())->exists();
        if ($ownedSppg) {
            return null;
        }

        abort(403, 'Anda tidak memiliki akses ke SPPG ini.');
    }

    private function buildLatestVendorPriceMap(array $itemIds): array
    {
        if (empty($itemIds)) {
            return [];
        }

        $rows = ItemVendor::with('vendor:id,kode_vendor,nama,status')
            ->whereIn('item_id', $itemIds)
            ->orderBy('item_id')
            ->orderBy('vendor_id')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get(['id', 'item_id', 'vendor_id', 'harga', 'tanggal']);

        $latestByItemVendor = [];
        foreach ($rows as $row) {
            $key = $row->item_id . '-' . $row->vendor_id;
            if (isset($latestByItemVendor[$key])) {
                continue;
            }

            if (!$row->vendor || $row->vendor->status !== 'aktif') {
                continue;
            }

            $latestByItemVendor[$key] = [
                'vendor_id' => (int) $row->vendor_id,
                'vendor_label' => ($row->vendor->kode_vendor ?? '-') . ' - ' . ($row->vendor->nama ?? '-'),
                'harga' => (float) $row->harga,
            ];
        }

        $result = [];
        foreach ($latestByItemVendor as $key => $value) {
            [$itemId] = explode('-', $key);
            $itemId = (int) $itemId;
            if (!isset($result[$itemId])) {
                $result[$itemId] = [];
            }
            $result[$itemId][] = $value;
        }

        foreach ($result as $itemId => $options) {
            usort($options, function ($a, $b) {
                return strcmp($a['vendor_label'], $b['vendor_label']);
            });
            $result[$itemId] = $options;
        }

        return $result;
    }

    private function getScopedActiveVendorsBySppg(int $sppgId)
    {
        return Vendor::query()
            ->where('status', 'aktif')
            ->whereHas('itemPrices', function ($q) use ($sppgId) {
                $q->where('sppg_id', $sppgId);
            })
            ->orderBy('nama')
            ->get(['id', 'kode_vendor', 'nama']);
    }

    private function ensureDraftStatus(PurchaseOrder $purchaseOrder)
    {
        if ((int) $purchaseOrder->status === self::STATUS_DRAFT) {
            return null;
        }

        return back()->with('error', 'Purchase Order hanya dapat diubah saat status masih Drafted.');
    }

    private function ensureReceivedAccessible(PurchaseOrder $purchaseOrder): void
    {
        if (!$this->isEmployeeRoleOne()) {
            abort(403, 'Halaman penerimaan barang hanya bisa diakses role employee (role 1).');
        }

        $this->ensureEmployeeCanAccessSppgForView((int) $purchaseOrder->sppg_id);
    }

    private function isEmployeeRoleOne(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->roles()
            ->where('id', self::EMPLOYEE_ROLE_ID)
            ->where('name', 'employee')
            ->exists();
    }

    private function generateKodePo(): string
    {
        $prefix = 'PO-' . now()->format('Ymd') . '-';
        $latest = PurchaseOrder::withTrashed()
            ->where('kode_po', 'like', $prefix . '%')
            ->orderBy('kode_po', 'desc')
            ->value('kode_po');

        $lastNumber = 0;
        if (!empty($latest)) {
            $parts = explode('-', $latest);
            $lastNumber = isset($parts[2]) ? (int) $parts[2] : 0;
        }

        $next = $lastNumber + 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $title = 'Purchase Order';
        $tableUrl = url('purchase-order/get_table');
        $data = $this->getSppgOptions();

        return view('purchase_order.index', array_merge($data, compact('title', 'tableUrl')));
    }

    public function get_table(Request $request)
    {
        $approverSppgIds = Auth::user()->hasAnyRole(['akuntan', 'verval', 'head'])
            ? Auth::user()->sppgs()->pluck('sppg.id')->map(fn ($id) => (int) $id)->all()
            : [];

        $query = PurchaseOrder::with(['sppg', 'details'])
            ->when(Auth::user()->hasRole\('perwakilan\ yayasan'\), function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->when(Auth::user()->hasAnyRole(['akuntan', 'verval', 'head']), function ($q) use ($approverSppgIds) {
                $q->whereIn('sppg_id', $approverSppgIds);
            })
            ->when($request->filled('filter_sppg_id'), function ($q) use ($request) {
                $q->where('sppg_id', $request->filter_sppg_id);
            })
            ->when($request->filled('filter_tanggal_start'), function ($q) use ($request) {
                $q->whereDate('tanggal_po', '>=', $request->filter_tanggal_start);
            })
            ->when($request->filled('filter_tanggal_end'), function ($q) use ($request) {
                $q->whereDate('tanggal_po', '<=', $request->filter_tanggal_end);
            })
            ->orderBy('tanggal_po', 'desc')
            ->orderBy('id', 'desc');

        $purchaseOrders = $query->get();
        $no = 0;

        return view('purchase_order.table', compact('purchaseOrders', 'no'));
    }

    public function generate(Request $request)
    {
        if (!Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            abort(403, 'Hanya role employee yang dapat generate Purchase Order.');
        }

        $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'tanggal_menu_dari' => 'required|date',
            'tanggal_menu_sampai' => 'required|date|after_or_equal:tanggal_menu_dari',
        ]);

        $sppgId = (int) $request->sppg_id;
        $this->ensureEmployeeCanAccessSppgForView($sppgId);

        $items = Item::with('uom:id,nama')
            ->where('sppg_id', $sppgId)
            ->whereHas('menus', function ($q) use ($request, $sppgId) {
                $q->where('menu.sppg_id', $sppgId)
                    ->whereDate('menu.tanggal', '>=', $request->tanggal_menu_dari)
                    ->whereDate('menu.tanggal', '<=', $request->tanggal_menu_sampai);
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'uom_id']);

        if ($items->isEmpty()) {
            return redirect()->route('purchase_order')->with('error', 'Tidak ada item menu pada rentang tanggal tersebut.');
        }

        $itemIds = $items->pluck('id')->map(fn ($id) => (int) $id)->all();
        $vendorOptionsByItem = $this->buildLatestVendorPriceMap($itemIds);
        $sppg = Sppg::findOrFail($sppgId);
        $uomOptions = Uom::where('sppg_id', $sppgId)
            ->orderBy('nama')
            ->get(['id', 'nama']);
        $customVendors = $this->getScopedActiveVendorsBySppg($sppgId);

        return view('purchase_order.generate', [
            'sppg' => $sppg,
            'items' => $items,
            'vendorOptionsByItem' => $vendorOptionsByItem,
            'uomOptions' => $uomOptions,
            'customVendors' => $customVendors,
            'tanggalMenuDari' => $request->tanggal_menu_dari,
            'tanggalMenuSampai' => $request->tanggal_menu_sampai,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            abort(403, 'Hanya role employee yang dapat membuat Purchase Order.');
        }

        $baseValidation = [
            'sppg_id' => 'required|exists:sppg,id',
            'tanggal_menu_dari' => 'required|date',
            'tanggal_menu_sampai' => 'required|date|after_or_equal:tanggal_menu_dari',
        ];

        $hasRegularItems = !empty($request->item_ids);
        $hasCustomItems = !empty($request->custom_item_names);

        if (!$hasRegularItems && !$hasCustomItems) {
            return back()->withInput()->with('error', 'Minimal harus ada 1 item (regular atau custom).');
        }

        if ($hasRegularItems) {
            $baseValidation += [
                'item_ids' => 'array',
                'item_ids.*' => 'required|integer|exists:item,id',
                'vendor_ids' => 'array',
                'vendor_ids.*' => 'required|integer|exists:vendor,id',
                'qtys' => 'array',
                'qtys.*' => 'required|numeric|gt:0',
            ];
        }

        if ($hasCustomItems) {
            $baseValidation += [
                'custom_item_names' => 'array',
                'custom_item_names.*' => 'required|string|max:255',
                'custom_item_satuan' => 'array',
                'custom_item_satuan.*' => 'nullable|string|max:100',
                'custom_item_uom_ids' => 'array',
                'custom_item_uom_ids.*' => 'nullable|integer|exists:uom,id',
                'custom_item_vendor_ids' => 'array',
                'custom_item_vendor_ids.*' => 'required|integer|exists:vendor,id',
                'custom_item_qtys' => 'array',
                'custom_item_qtys.*' => 'required|numeric|gt:0',
            ];
        }

        $request->validate($baseValidation);

        $sppgId = (int) $request->sppg_id;
        if ($response = $this->ensureEmployeeCanAccessSppg($sppgId)) {
            return $response;
        }

        $itemIds = $hasRegularItems ? collect($request->item_ids)->map(fn ($id) => (int) $id)->values() : collect();
        $vendorIds = $hasRegularItems ? collect($request->vendor_ids)->map(fn ($id) => (int) $id)->values() : collect();
        $qtys = $hasRegularItems ? collect($request->qtys)->map(fn ($qty) => (float) $qty)->values() : collect();

        if ($hasRegularItems) {
            if ($itemIds->count() !== $vendorIds->count() || $itemIds->count() !== $qtys->count()) {
                return back()->withInput()->with('error', 'Data item, vendor, dan qty tidak valid.');
            }

            if ($itemIds->unique()->count() !== $itemIds->count()) {
                return back()->withInput()->with('error', 'Item pada detail purchase order tidak boleh duplikat.');
            }

            $validItemIds = Item::where('sppg_id', $sppgId)->whereIn('id', $itemIds->all())->pluck('id')->map(fn ($id) => (int) $id);
            if ($validItemIds->count() !== $itemIds->count()) {
                return back()->withInput()->with('error', 'Terdapat item yang tidak sesuai dengan SPPG.');
            }

            $menuItemIds = Menu::where('sppg_id', $sppgId)
                ->whereDate('tanggal', '>=', $request->tanggal_menu_dari)
                ->whereDate('tanggal', '<=', $request->tanggal_menu_sampai)
                ->with('items:id')
                ->get()
                ->pluck('items')
                ->flatten()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->unique();

            $outsidePeriod = $itemIds->diff($menuItemIds);
            if ($outsidePeriod->isNotEmpty()) {
                return back()->withInput()->with('error', 'Terdapat item yang tidak masuk periode menu yang dipilih.');
            }

            $latestRows = ItemVendor::whereIn('item_id', $itemIds->all())
                ->whereIn('vendor_id', $vendorIds->all())
                ->orderBy('item_id')
                ->orderBy('vendor_id')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->get(['item_id', 'vendor_id', 'harga']);

            $latestPriceMap = [];
            foreach ($latestRows as $row) {
                $key = ((int) $row->item_id) . '-' . ((int) $row->vendor_id);
                if (!isset($latestPriceMap[$key])) {
                    $latestPriceMap[$key] = (float) $row->harga;
                }
            }

            foreach ($itemIds as $index => $itemId) {
                $vendorId = (int) $vendorIds[$index];
                $priceKey = $itemId . '-' . $vendorId;
                if (!isset($latestPriceMap[$priceKey])) {
                    return back()->withInput()->with('error', 'Harga item untuk vendor yang dipilih tidak ditemukan.');
                }
            }
        }

        $customItemNames = $hasCustomItems ? collect($request->custom_item_names)->values() : collect();
        $customItemSatuan = $hasCustomItems ? collect($request->custom_item_satuan)->values() : collect();
        $customItemUomIds = $hasCustomItems ? collect($request->custom_item_uom_ids)->map(fn ($id) => (int) $id)->values() : collect();
        $customItemVendorIds = $hasCustomItems ? collect($request->custom_item_vendor_ids)->map(fn ($id) => (int) $id)->values() : collect();
        $customItemQtys = $hasCustomItems ? collect($request->custom_item_qtys)->map(fn ($qty) => (float) $qty)->values() : collect();

        if ($hasCustomItems) {
            if ($customItemNames->count() !== $customItemVendorIds->count() || 
                $customItemNames->count() !== $customItemQtys->count() || 
                $customItemNames->count() !== $customItemUomIds->count() ||
                $customItemNames->count() !== $customItemSatuan->count()) {
                return back()->withInput()->with('error', 'Data custom item tidak valid.');
            }

            $selectedUomIds = $customItemUomIds->filter(fn ($id) => (int) $id > 0)->values();
            $validUomMap = Uom::where('sppg_id', $sppgId)
                ->whereIn('id', $selectedUomIds->all())
                ->pluck('nama', 'id');

            if ($validUomMap->count() !== $selectedUomIds->unique()->count()) {
                return back()->withInput()->with('error', 'Terdapat UOM custom item yang tidak sesuai dengan SPPG.');
            }

            $validVendorIds = ItemVendor::where('sppg_id', $sppgId)
                ->whereIn('vendor_id', $customItemVendorIds->all())
                ->distinct()
                ->pluck('vendor_id')
                ->map(fn ($id) => (int) $id);

            if ($validVendorIds->count() !== $customItemVendorIds->unique()->count()) {
                return back()->withInput()->with('error', 'Terdapat vendor custom item yang tidak sesuai dengan SPPG.');
            }

            $uomNameMap = $validUomMap->mapWithKeys(function ($name, $id) {
                return [(int) $id => $name];
            })->all();
        } else {
            $uomNameMap = [];
        }

        $latestPriceMap = isset($latestPriceMap) ? $latestPriceMap : [];
        $header = null;

        DB::transaction(function () use ($request, $itemIds, $vendorIds, $qtys, $latestPriceMap, $sppgId, 
                                         $hasRegularItems, $hasCustomItems, $customItemNames, $customItemUomIds,
                                         $customItemVendorIds, $customItemQtys, $customItemSatuan, $uomNameMap, &$header) {
            $detailsPayload = [];
            $grandTotal = 0;

            if ($hasRegularItems) {
                foreach ($itemIds as $index => $itemId) {
                    $vendorId = (int) $vendorIds[$index];
                    $qty = (float) $qtys[$index];
                    $priceKey = $itemId . '-' . $vendorId;

                    $harga = (float) $latestPriceMap[$priceKey];
                    $subtotal = $qty * $harga;
                    $grandTotal += $subtotal;

                    $detailsPayload[] = [
                        'item_id' => $itemId,
                        'vendor_id' => $vendorId,
                        'qty' => $qty,
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                    ];
                }
            }

            if ($hasCustomItems) {
                foreach ($customItemNames as $index => $name) {
                    $vendorId = (int) $customItemVendorIds[$index];
                    $qty = (float) $customItemQtys[$index];
                    $harga = 0;

                    $latestPrice = ItemVendor::where('vendor_id', $vendorId)
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('id', 'desc')
                        ->value('harga');

                    if ($latestPrice) {
                        $harga = (float) $latestPrice;
                    }

                    $subtotal = $qty * $harga;
                    $grandTotal += $subtotal;

                    $detailsPayload[] = [
                        'item_id' => null,
                        'custom_item_name' => $name,
                        'custom_uom_id' => (int) $customItemUomIds[$index] > 0 ? (int) $customItemUomIds[$index] : null,
                        'custom_item_satuan' => $uomNameMap[(int) $customItemUomIds[$index]] ?? ($customItemSatuan[$index] ?? '-'),
                        'vendor_id' => $vendorId,
                        'qty' => $qty,
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                    ];
                }
            }

            $header = PurchaseOrder::create([
                'sppg_id' => $sppgId,
                'kode_po' => $this->generateKodePo(),
                'tanggal_po' => now()->toDateString(),
                'tanggal_menu_dari' => $request->tanggal_menu_dari,
                'tanggal_menu_sampai' => $request->tanggal_menu_sampai,
                'total_bayar' => $grandTotal,
                'status' => self::STATUS_DRAFT,
            ]);

            $header->details()->createMany($detailsPayload);
        });

        return redirect()->route('purchase_order')->with('success', 'Purchase Order berhasil dibuat dengan kode ' . $header->kode_po . '.');
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            abort(403, 'Hanya role employee yang dapat mengedit Purchase Order.');
        }

        $this->ensureEmployeeCanAccessSppgForView((int) $purchaseOrder->sppg_id);

        if ((int) $purchaseOrder->status !== self::STATUS_DRAFT) {
            return redirect()->route('purchase_order')->with('error', 'Purchase Order dengan status selain Drafted tidak dapat diedit.');
        }

        $purchaseOrder->load([
            'sppg:id,nama',
            'details.item:id,nama,uom_id',
            'details.item.uom:id,nama',
            'details.customUom:id,nama',
            'details.vendor:id,kode_vendor,nama',
        ]);

        $itemIds = $purchaseOrder->details->whereNotNull('item_id')->pluck('item_id')->map(fn ($id) => (int) $id)->unique()->values()->all();
        $vendorOptionsByItem = $this->buildLatestVendorPriceMap($itemIds);

        foreach ($purchaseOrder->details as $detail) {
            if (!$detail->item_id) {
                continue;
            }
            
            $itemId = (int) $detail->item_id;
            if (!isset($vendorOptionsByItem[$itemId])) {
                $vendorOptionsByItem[$itemId] = [];
            }

            $exists = collect($vendorOptionsByItem[$itemId])->contains(function ($option) use ($detail) {
                return (int) $option['vendor_id'] === (int) $detail->vendor_id;
            });

            if (!$exists) {
                $vendorOptionsByItem[$itemId][] = [
                    'vendor_id' => (int) $detail->vendor_id,
                    'vendor_label' => ($detail->vendor->kode_vendor ?? '-') . ' - ' . ($detail->vendor->nama ?? '-'),
                    'harga' => (float) $detail->harga,
                ];
            }

            usort($vendorOptionsByItem[$itemId], function ($a, $b) {
                return strcmp($a['vendor_label'], $b['vendor_label']);
            });
        }

        $uomOptions = Uom::where('sppg_id', (int) $purchaseOrder->sppg_id)
            ->orderBy('nama')
            ->get(['id', 'nama']);
        $customVendors = $this->getScopedActiveVendorsBySppg((int) $purchaseOrder->sppg_id);

        return view('purchase_order.edit', [
            'purchaseOrder' => $purchaseOrder,
            'vendorOptionsByItem' => $vendorOptionsByItem,
            'uomOptions' => $uomOptions,
            'customVendors' => $customVendors,
        ]);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $this->ensureEmployeeCanAccessSppgForView((int) $purchaseOrder->sppg_id);
        }

        $this->ensureApproverCanAccessSppg($purchaseOrder);

        $this->loadPurchaseOrderForDocument($purchaseOrder);
        $vendorGroups = $this->buildVendorDocumentGroups($purchaseOrder);

        return view('purchase_order.show', [
            'purchaseOrder' => $purchaseOrder,
            'vendorGroups' => $vendorGroups,
        ]);
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $this->ensureEmployeeCanAccessSppgForView((int) $purchaseOrder->sppg_id);
        }

        $this->ensureApproverCanAccessSppg($purchaseOrder);

        $this->loadPurchaseOrderForDocument($purchaseOrder);
        $vendorGroups = $this->buildVendorDocumentGroups($purchaseOrder);

        $pdf = Pdf::loadView('purchase_order.pdf', [
            'purchaseOrder' => $purchaseOrder,
            'vendorGroups' => $vendorGroups,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('PO-' . $purchaseOrder->kode_po . '.pdf');
    }

    public function downloadPdfPerVendor(PurchaseOrder $purchaseOrder, int $vendorId)
    {
        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $this->ensureEmployeeCanAccessSppgForView((int) $purchaseOrder->sppg_id);
        }

        $this->ensureApproverCanAccessSppg($purchaseOrder);

        $this->loadPurchaseOrderForDocument($purchaseOrder);
        $vendorGroups = $this->buildVendorDocumentGroups($purchaseOrder);
        $selectedGroup = $vendorGroups->first(function ($group) use ($vendorId) {
            return (int) optional($group['vendor'])->id === $vendorId;
        });

        if (!$selectedGroup) {
            abort(404, 'Vendor tidak ditemukan pada Purchase Order ini.');
        }

        $pdf = Pdf::loadView('purchase_order.pdf', [
            'purchaseOrder' => $purchaseOrder,
            'vendorGroups' => collect([$selectedGroup]),
        ])->setPaper('a4', 'landscape');

        $vendorCode = optional($selectedGroup['vendor'])->kode_vendor ?: 'vendor';

        return $pdf->download('PO-' . $purchaseOrder->kode_po . '-' . $vendorCode . '.pdf');
    }

    public function received(PurchaseOrder $purchaseOrder)
    {
        $this->ensureReceivedAccessible($purchaseOrder);

        $this->loadPurchaseOrderForDocument($purchaseOrder);
        $vendorGroups = $this->buildVendorDocumentGroups($purchaseOrder);

        return view('purchase_order.received', [
            'purchaseOrder' => $purchaseOrder,
            'vendorGroups' => $vendorGroups,
            'isEditable' => $this->isEmployeeRoleOne(),
        ]);
    }

    public function storeReceived(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureReceivedAccessible($purchaseOrder);

        if (!$this->isEmployeeRoleOne()) {
            abort(403, 'Hanya role employee (role 1) yang dapat menyimpan penerimaan barang.');
        }

        $purchaseOrder->load(['details', 'vendorReceipts']);

        $detailIds = $purchaseOrder->details->pluck('id')->map(fn ($id) => (int) $id)->all();
        $vendorIds = $purchaseOrder->details->pluck('vendor_id')->filter()->map(fn ($id) => (int) $id)->unique()->values()->all();

        $request->validate([
            'received_qtys' => 'array',
            'received_qtys.*' => 'nullable|numeric|min:0',
            'realized_prices' => 'array',
            'realized_prices.*' => 'nullable|numeric|min:0',
            'vendor_receipts' => 'array',
            'vendor_receipts.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => ['required', 'in:0,1,2,3,4'],
        ]);

        $newStatus = (int) $request->input('status');

        DB::transaction(function () use ($request, $purchaseOrder, $detailIds, $vendorIds, $newStatus) {
            $totalRealisasi = 0;

            foreach ($purchaseOrder->details as $detail) {
                if (!in_array((int) $detail->id, $detailIds, true)) {
                    continue;
                }

                $receivedQty = $request->input('received_qtys.' . $detail->id);
                $realizedPrice = $request->input('realized_prices.' . $detail->id);

                $qtyDiterima = $receivedQty === null || $receivedQty === '' ? null : (float) $receivedQty;
                $hargaRealisasi = $realizedPrice === null || $realizedPrice === '' ? null : (float) $realizedPrice;
                $subtotalRealisasi = ($qtyDiterima !== null && $hargaRealisasi !== null)
                    ? $qtyDiterima * $hargaRealisasi
                    : null;

                $detail->update([
                    'qty_diterima' => $qtyDiterima,
                    'harga_realisasi' => $hargaRealisasi,
                    'subtotal_realisasi' => $subtotalRealisasi,
                ]);

                if ($subtotalRealisasi !== null) {
                    $totalRealisasi += $subtotalRealisasi;
                }
            }

            foreach ($vendorIds as $vendorId) {
                if (!$request->hasFile('vendor_receipts.' . $vendorId)) {
                    continue;
                }

                $existingReceipt = $purchaseOrder->vendorReceipts->firstWhere('vendor_id', $vendorId);
                if ($existingReceipt && $existingReceipt->nota_path) {
                    Storage::disk('public')->delete($existingReceipt->nota_path);
                }

                $path = $request->file('vendor_receipts.' . $vendorId)
                    ->store('purchase-order-receipts', 'public');

                $purchaseOrder->vendorReceipts()->updateOrCreate(
                    ['vendor_id' => $vendorId],
                    ['nota_path' => $path]
                );
            }

            $oldStatus = (int) $purchaseOrder->status;
            $purchaseOrder->update(['status' => $newStatus]);

            // Deduct saldo if status becomes 4
            if ($newStatus === 4 && $oldStatus !== 4) {
                $purchaseOrder->sppg->decrement('saldo', $totalRealisasi);

                \App\Models\CashOut::create([
                    'jenis_cashout_id' => 1,
                    'sppg_id' => $purchaseOrder->sppg_id,
                    'nominal' => $totalRealisasi,
                    'tanggal' => now()->toDateString(),
                    'keterangan' => 'di generate oleh sistem setelah PO disetujui kepala SPPG',
                ]);
            }
        });

        if ($newStatus === 4) {
            return redirect()
                ->route('purchase_order')
                ->with('success', 'Penerimaan barang diselesaikan dan saldo SPPG telah dipotong.');
        }

        return redirect()
            ->route('purchase_order.received', $purchaseOrder->id)
            ->with('success', 'Data penerimaan barang berhasil disimpan.');
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            abort(403, 'Hanya role employee yang dapat memperbarui Purchase Order.');
        }

        if ($response = $this->ensureEmployeeCanAccessSppg((int) $purchaseOrder->sppg_id)) {
            return $response;
        }

        if ($draftValidation = $this->ensureDraftStatus($purchaseOrder)) {
            return $draftValidation;
        }

        $purchaseOrder->load('details');

        $baseValidation = [
            'tanggal_menu_dari' => 'required|date',
            'tanggal_menu_sampai' => 'required|date|after_or_equal:tanggal_menu_dari',
        ];

        $hasRegularItems = !empty($request->detail_ids);
        $hasCustomItems = !empty($request->custom_item_names);

        if (!$hasRegularItems && !$hasCustomItems) {
            return back()->withInput()->with('error', 'Minimal harus ada 1 item (regular atau custom).');
        }

        if ($hasRegularItems) {
            $baseValidation += [
                'detail_ids' => 'required|array|min:1',
                'detail_ids.*' => [
                    'required',
                    'integer',
                    Rule::in($purchaseOrder->details->whereNotNull('item_id')->pluck('id')->map(fn ($id) => (int) $id)->all()),
                ],
                'vendor_ids' => 'required|array|min:1',
                'vendor_ids.*' => 'required|integer|exists:vendor,id',
                'qtys' => 'required|array|min:1',
                'qtys.*' => 'required|numeric|gt:0',
            ];
        }

        if ($hasCustomItems) {
            $baseValidation += [
                'custom_item_names' => 'array',
                'custom_item_names.*' => 'required|string|max:255',
                'custom_item_satuan' => 'array',
                'custom_item_satuan.*' => 'nullable|string|max:100',
                'custom_item_uom_ids' => 'array',
                'custom_item_uom_ids.*' => 'nullable|integer|exists:uom,id',
                'custom_item_vendor_ids' => 'array',
                'custom_item_vendor_ids.*' => 'required|integer|exists:vendor,id',
                'custom_item_qtys' => 'array',
                'custom_item_qtys.*' => 'required|numeric|gt:0',
            ];
        }

        $request->validate($baseValidation);

        $detailIds = $hasRegularItems ? collect($request->detail_ids)->map(fn ($id) => (int) $id)->values() : collect();
        $vendorIds = $hasRegularItems ? collect($request->vendor_ids)->map(fn ($id) => (int) $id)->values() : collect();
        $qtys = $hasRegularItems ? collect($request->qtys)->map(fn ($qty) => (float) $qty)->values() : collect();

        if ($hasRegularItems) {
            if ($detailIds->count() !== $vendorIds->count() || $detailIds->count() !== $qtys->count()) {
                return back()->withInput()->with('error', 'Data detail, vendor, dan qty tidak valid.');
            }

            if ($detailIds->unique()->count() !== $detailIds->count()) {
                return back()->withInput()->with('error', 'Detail purchase order tidak boleh duplikat.');
            }

            $detailsById = $purchaseOrder->details->keyBy('id');
            $itemIds = [];
            foreach ($detailIds as $detailId) {
                if (!isset($detailsById[$detailId])) {
                    return back()->withInput()->with('error', 'Terdapat detail purchase order yang tidak valid.');
                }
                $itemIds[] = (int) $detailsById[$detailId]->item_id;
            }

            $menuItemIds = Menu::where('sppg_id', $purchaseOrder->sppg_id)
                ->whereDate('tanggal', '>=', $request->tanggal_menu_dari)
                ->whereDate('tanggal', '<=', $request->tanggal_menu_sampai)
                ->with('items:id')
                ->get()
                ->pluck('items')
                ->flatten()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->unique();

            $outsidePeriod = collect($itemIds)->diff($menuItemIds);
            if ($outsidePeriod->isNotEmpty()) {
                return back()->withInput()->with('error', 'Terdapat item yang tidak masuk periode menu yang dipilih.');
            }

            $latestRows = ItemVendor::whereIn('item_id', $itemIds)
                ->whereIn('vendor_id', $vendorIds->all())
                ->orderBy('item_id')
                ->orderBy('vendor_id')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->get(['item_id', 'vendor_id', 'harga']);

            $latestPriceMap = [];
            foreach ($latestRows as $row) {
                $key = ((int) $row->item_id) . '-' . ((int) $row->vendor_id);
                if (!isset($latestPriceMap[$key])) {
                    $latestPriceMap[$key] = (float) $row->harga;
                }
            }

            foreach ($detailIds as $index => $detailId) {
                $itemId = (int) $detailsById[$detailId]->item_id;
                $vendorId = (int) $vendorIds[$index];
                $priceKey = $itemId . '-' . $vendorId;

                if (!isset($latestPriceMap[$priceKey])) {
                    return back()->withInput()->with('error', 'Harga item untuk vendor yang dipilih tidak ditemukan.');
                }
            }
        }

        $customItemNames = $hasCustomItems ? collect($request->custom_item_names)->values() : collect();
        $customItemSatuan = $hasCustomItems ? collect($request->custom_item_satuan)->values() : collect();
        $customItemUomIds = $hasCustomItems ? collect($request->custom_item_uom_ids)->map(fn ($id) => (int) $id)->values() : collect();
        $customItemVendorIds = $hasCustomItems ? collect($request->custom_item_vendor_ids)->map(fn ($id) => (int) $id)->values() : collect();
        $customItemQtys = $hasCustomItems ? collect($request->custom_item_qtys)->map(fn ($qty) => (float) $qty)->values() : collect();

        if ($hasCustomItems) {
            if ($customItemNames->count() !== $customItemVendorIds->count() || 
                $customItemNames->count() !== $customItemQtys->count() || 
                $customItemNames->count() !== $customItemUomIds->count() ||
                $customItemNames->count() !== $customItemSatuan->count()) {
                return back()->withInput()->with('error', 'Data custom item tidak valid.');
            }

            $selectedUomIds = $customItemUomIds->filter(fn ($id) => (int) $id > 0)->values();
            $validUomMap = Uom::where('sppg_id', (int) $purchaseOrder->sppg_id)
                ->whereIn('id', $selectedUomIds->all())
                ->pluck('nama', 'id');

            if ($validUomMap->count() !== $selectedUomIds->unique()->count()) {
                return back()->withInput()->with('error', 'Terdapat UOM custom item yang tidak sesuai dengan SPPG.');
            }

            $validVendorIds = ItemVendor::where('sppg_id', (int) $purchaseOrder->sppg_id)
                ->whereIn('vendor_id', $customItemVendorIds->all())
                ->distinct()
                ->pluck('vendor_id')
                ->map(fn ($id) => (int) $id);

            if ($validVendorIds->count() !== $customItemVendorIds->unique()->count()) {
                return back()->withInput()->with('error', 'Terdapat vendor custom item yang tidak sesuai dengan SPPG.');
            }

            $uomNameMap = $validUomMap->mapWithKeys(function ($name, $id) {
                return [(int) $id => $name];
            })->all();
        } else {
            $uomNameMap = [];
        }

        $latestPriceMap = isset($latestPriceMap) ? $latestPriceMap : [];
        $detailsById = isset($detailsById) ? $detailsById : $purchaseOrder->details->keyBy('id');

        DB::transaction(function () use ($request, $purchaseOrder, $detailIds, $vendorIds, $qtys, $latestPriceMap, $detailsById,
                                         $hasRegularItems, $hasCustomItems, $customItemNames, $customItemUomIds,
                                         $customItemVendorIds, $customItemQtys, $customItemSatuan, $uomNameMap) {
            $grandTotal = 0;

            // Delete all details dan create ulang dengan data baru
            $purchaseOrder->details()->delete();
            $newDetailsPayload = [];

            if ($hasRegularItems) {
                foreach ($detailIds as $index => $detailId) {
                    $detail = $detailsById[$detailId];
                    $itemId = (int) $detail->item_id;
                    $vendorId = (int) $vendorIds[$index];
                    $qty = (float) $qtys[$index];
                    $harga = (float) $latestPriceMap[$itemId . '-' . $vendorId];
                    $subtotal = $qty * $harga;

                    $newDetailsPayload[] = [
                        'item_id' => $itemId,
                        'vendor_id' => $vendorId,
                        'qty' => $qty,
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                    ];

                    $grandTotal += $subtotal;
                }
            }

            if ($hasCustomItems) {
                foreach ($customItemNames as $index => $name) {
                    $vendorId = (int) $customItemVendorIds[$index];
                    $qty = (float) $customItemQtys[$index];
                    $harga = 0;

                    $latestPrice = ItemVendor::where('vendor_id', $vendorId)
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('id', 'desc')
                        ->value('harga');

                    if ($latestPrice) {
                        $harga = (float) $latestPrice;
                    }

                    $subtotal = $qty * $harga;
                    $newDetailsPayload[] = [
                        'item_id' => null,
                        'custom_item_name' => $name,
                        'custom_uom_id' => (int) $customItemUomIds[$index] > 0 ? (int) $customItemUomIds[$index] : null,
                        'custom_item_satuan' => $uomNameMap[(int) $customItemUomIds[$index]] ?? ($customItemSatuan[$index] ?? '-'),
                        'vendor_id' => $vendorId,
                        'qty' => $qty,
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                    ];

                    $grandTotal += $subtotal;
                }
            }

            $purchaseOrder->details()->createMany($newDetailsPayload);

            $purchaseOrder->update([
                'tanggal_menu_dari' => $request->tanggal_menu_dari,
                'tanggal_menu_sampai' => $request->tanggal_menu_sampai,
                'total_bayar' => $grandTotal,
            ]);
        });

        return redirect()->route('purchase_order')->with('success', 'Purchase Order ' . $purchaseOrder->kode_po . ' berhasil diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            abort(403, 'Hanya role employee yang dapat menghapus Purchase Order.');
        }

        if ($response = $this->ensureEmployeeCanAccessSppg((int) $purchaseOrder->sppg_id)) {
            return $response;
        }

        if ($draftValidation = $this->ensureDraftStatus($purchaseOrder)) {
            return $draftValidation;
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase_order')->with('success', 'Purchase Order ' . $purchaseOrder->kode_po . ' berhasil dihapus.');
    }

    public function requestApproval(PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            abort(403, 'Hanya role employee yang dapat mengajukan Purchase Order.');
        }

        if ($response = $this->ensureEmployeeCanAccessSppg((int) $purchaseOrder->sppg_id)) {
            return $response;
        }

        if ((int) $purchaseOrder->status !== self::STATUS_DRAFT) {
            return back()->with('error', 'Purchase Order hanya bisa diajukan saat status masih Drafted.');
        }

        $purchaseOrder->update([
            'status' => self::STATUS_RECEIVED,
            'last_rejection_comment' => null,
            'last_rejected_by_role' => null,
        ]);

        return redirect()->route('purchase_order')->with('success', 'Purchase Order ' . $purchaseOrder->kode_po . ' berhasil ditandai sebagai Penerimaan Barang.');
    }

    public function review(Request $request, PurchaseOrder $purchaseOrder, string $stage)
    {
        $config = $this->getReviewStageConfig($stage);

        if (!Auth::user()->hasRole($config['role'])) {
            abort(403, 'Anda tidak memiliki akses untuk tahap review ini.');
        }

        $this->ensureApproverCanAccessSppg($purchaseOrder);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'comment' => 'nullable|string|max:1000',
        ]);

        $action = $request->input('action');
        $comment = trim((string) $request->input('comment'));

        if ((int) $purchaseOrder->status !== (int) $config['expected_status']) {
            return back()->with('error', 'Status Purchase Order tidak sesuai untuk proses review tahap ini.');
        }

        if ($action === 'reject' && $comment === '') {
            return back()->with('error', 'Komentar wajib diisi saat tidak menyetujui Purchase Order.');
        }

        $payload = [
            $config['comment_field'] => $comment !== '' ? $comment : null,
        ];

        if ($action === 'approve') {
            $payload['status'] = (int) $config['approved_status'];
            $payload['last_rejection_comment'] = null;
            $payload['last_rejected_by_role'] = null;
        } else {
            $payload['status'] = self::STATUS_DRAFT;
            $payload['last_rejection_comment'] = $comment;
            $payload['last_rejected_by_role'] = $config['role'];
        }

        $purchaseOrder->update($payload);

        if ($action === 'approve' && isset($payload['status']) && $payload['status'] === self::STATUS_APPROVED_HEAD) {
            $totalRealisasi = $purchaseOrder->details()->sum('subtotal_realisasi') ?? 0;
            $purchaseOrder->sppg->decrement('saldo', $totalRealisasi);

            \App\Models\CashOut::create([
                'jenis_cashout_id' => 1,
                'sppg_id' => $purchaseOrder->sppg_id,
                'nominal' => $totalRealisasi,
                'tanggal' => now()->toDateString(),
                'keterangan' => 'di generate oleh sistem setelah PO disetujui kepala SPPG',
            ]);
        }

        if ($action === 'approve') {
            return redirect()->route('purchase_order')->with('success', 'Purchase Order ' . $purchaseOrder->kode_po . ' berhasil disetujui oleh ' . ucfirst($config['role']) . '.');
        }

        return redirect()->route('purchase_order')->with('success', 'Purchase Order ' . $purchaseOrder->kode_po . ' tidak disetujui oleh ' . ucfirst($config['role']) . ' dan dikembalikan ke Draft.');
    }
}

