<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemVendor;
use App\Models\Sppg;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemVendorController extends Controller
{
    private function isHr(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    private function ensureHrAccess()
    {
        if (!$this->isHr()) {
            return response()->json(['message' => 'Hanya HR yang dapat melakukan aksi ini.'], 403);
        }

        return null;
    }

    private function getLatestIdsByItemVendor(bool $isEmployee): array
    {
        $rows = ItemVendor::select(['id', 'item_id', 'vendor_id', 'tanggal'])
            ->when($isEmployee, function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->orderBy('item_id')
            ->orderBy('vendor_id')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $latestIds = [];
        $seen = [];

        foreach ($rows as $row) {
            $key = $row->item_id . '-' . $row->vendor_id;
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $latestIds[] = (int) $row->id;
        }

        return $latestIds;
    }

    private function getMasterData(): array
    {
        $isEmployee = Auth::user()->hasRole\('perwakilan\ yayasan'\);

        $sppg = $isEmployee
            ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get()
            : Sppg::orderBy('nama')->get();

        $items = Item::when($isEmployee, function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->with('uom:id,nama')
            ->orderBy('nama')
            ->get();

        $vendors = Vendor::where('status', 'aktif')->orderBy('nama')->get();

        return compact('isEmployee', 'sppg', 'items', 'vendors');
    }

    public function index()
    {
        $title = 'Harga Vendor Item';
        $tableUrl = url('item-vendor/get_table');

        $data = $this->getMasterData();
        return view('item_vendor.index', array_merge($data, compact('title', 'tableUrl')));
    }

    public function create()
    {
        $title = 'Add Harga Vendor';

        $data = $this->getMasterData();
        return view('item_vendor.create', array_merge($data, compact('title')));
    }

    public function get_table(Request $request)
    {
        $isEmployee = Auth::user()->hasRole\('perwakilan\ yayasan'\);
        $isHr = $this->isHr();

        $itemVendor = ItemVendor::with(['sppg', 'item', 'vendor'])
            ->when($isEmployee, function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->when($request->filled('filter_item_id'), function ($q) use ($request) {
                $q->where('item_id', $request->filter_item_id);
            })
            ->when($request->filled('filter_vendor_id'), function ($q) use ($request) {
                $q->where('vendor_id', $request->filter_vendor_id);
            })
            ->when($request->filled('filter_tanggal_start'), function ($q) use ($request) {
                $q->whereDate('tanggal', '>=', $request->filter_tanggal_start);
            })
            ->when($request->filled('filter_tanggal_end'), function ($q) use ($request) {
                $q->whereDate('tanggal', '<=', $request->filter_tanggal_end);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $latestIds = $this->getLatestIdsByItemVendor($isEmployee);

        $no = 0;
        return view('item_vendor.table', compact('itemVendor', 'no', 'latestIds', 'isEmployee', 'isHr'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'item_id' => 'required|exists:item,id',
            'vendor_id' => 'required|exists:vendor,id',
            'tanggal' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'rank' => 'required|integer|min:1',
        ]);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $ownedSppg = Sppg::where('id', $request->sppg_id)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
            }
        }

        $item = Item::findOrFail($request->item_id);
        if ((int) $item->sppg_id !== (int) $request->sppg_id) {
            return response()->json(['message' => 'Item harus berasal dari SPPG yang sama.'], 422);
        }

        DB::transaction(function () use ($request) {
            ItemVendor::create([
                'sppg_id' => $request->sppg_id,
                'item_id' => $request->item_id,
                'vendor_id' => $request->vendor_id,
                'tanggal' => $request->tanggal,
                'harga' => $request->harga,
                'rank' => $request->rank,
            ]);

            // If a new rank 1 is inserted, demote previous latest rank 1 from other vendors (same item) to rank 2.
            if ((int) $request->rank !== 1) {
                return;
            }

            $otherLatestRows = ItemVendor::select(['id', 'vendor_id', 'rank'])
                ->where('item_id', $request->item_id)
                ->where('vendor_id', '!=', $request->vendor_id)
                ->orderBy('vendor_id')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $latestIdsPerVendor = [];
            foreach ($otherLatestRows as $row) {
                if (!isset($latestIdsPerVendor[$row->vendor_id])) {
                    $latestIdsPerVendor[$row->vendor_id] = $row;
                }
            }

            $toDemoteIds = collect($latestIdsPerVendor)
                ->filter(function ($row) {
                    return (int) $row->rank === 1;
                })
                ->map(function ($row) {
                    return (int) $row->id;
                })
                ->values()
                ->all();

            if (!empty($toDemoteIds)) {
                ItemVendor::whereIn('id', $toDemoteIds)->update(['rank' => 2]);
            }
        });

        return response()->json('Saved');
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'rows'    => 'required|array|min:1',
            'rows.*.item_id'   => 'required|exists:item,id',
            'rows.*.vendor_id' => 'required|exists:vendor,id',
            'rows.*.tanggal'   => 'required|date',
            'rows.*.harga'     => 'required|numeric|min:0',
            'rows.*.rank'      => 'required|integer|min:1',
        ]);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $ownedSppg = Sppg::where('id', $request->sppg_id)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
            }
        }

        foreach ($request->rows as $row) {
            $item = Item::findOrFail($row['item_id']);
            if ((int) $item->sppg_id !== (int) $request->sppg_id) {
                return response()->json(['message' => 'Item "' . $item->nama . '" harus berasal dari SPPG yang sama.'], 422);
            }
        }

        DB::transaction(function () use ($request) {
            foreach ($request->rows as $row) {
                ItemVendor::create([
                    'sppg_id'   => $request->sppg_id,
                    'item_id'   => $row['item_id'],
                    'vendor_id' => $row['vendor_id'],
                    'tanggal'   => $row['tanggal'],
                    'harga'     => $row['harga'],
                    'rank'      => $row['rank'],
                ]);

                if ((int) $row['rank'] !== 1) {
                    continue;
                }

                $otherLatestRows = ItemVendor::select(['id', 'vendor_id', 'rank'])
                    ->where('item_id', $row['item_id'])
                    ->where('vendor_id', '!=', $row['vendor_id'])
                    ->orderBy('vendor_id')
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();

                $latestIdsPerVendor = [];
                foreach ($otherLatestRows as $r) {
                    if (!isset($latestIdsPerVendor[$r->vendor_id])) {
                        $latestIdsPerVendor[$r->vendor_id] = $r;
                    }
                }

                $toDemoteIds = collect($latestIdsPerVendor)
                    ->filter(function ($r) { return (int) $r->rank === 1; })
                    ->map(function ($r) { return (int) $r->id; })
                    ->values()
                    ->all();

                if (!empty($toDemoteIds)) {
                    ItemVendor::whereIn('id', $toDemoteIds)->update(['rank' => 2]);
                }
            }
        });

        return response()->json('Saved');
    }

    public function edit(string $id)
    {
        if ($response = $this->ensureHrAccess()) {
            return $response;
        }

        $itemVendor = ItemVendor::findOrFail($id);
        return response()->json(['item_vendor' => $itemVendor]);
    }

    public function update(Request $request, string $id)
    {
        if ($response = $this->ensureHrAccess()) {
            return $response;
        }

        $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'item_id' => 'required|exists:item,id',
            'vendor_id' => 'required|exists:vendor,id',
            'tanggal' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'rank' => 'required|integer|min:1',
        ]);

        $item = Item::findOrFail($request->item_id);
        if ((int) $item->sppg_id !== (int) $request->sppg_id) {
            return response()->json(['message' => 'Item harus berasal dari SPPG yang sama.'], 422);
        }

        DB::transaction(function () use ($request, $id) {
            $itemVendor = ItemVendor::findOrFail($id);
            $itemVendor->update([
                'sppg_id' => $request->sppg_id,
                'item_id' => $request->item_id,
                'vendor_id' => $request->vendor_id,
                'tanggal' => $request->tanggal,
                'harga' => $request->harga,
                'rank' => $request->rank,
            ]);

            if ((int) $request->rank !== 1) {
                return;
            }

            $otherLatestRows = ItemVendor::select(['id', 'vendor_id', 'rank'])
                ->where('item_id', $request->item_id)
                ->where('vendor_id', '!=', $request->vendor_id)
                ->orderBy('vendor_id')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $latestIdsPerVendor = [];
            foreach ($otherLatestRows as $row) {
                if (!isset($latestIdsPerVendor[$row->vendor_id])) {
                    $latestIdsPerVendor[$row->vendor_id] = $row;
                }
            }

            $toDemoteIds = collect($latestIdsPerVendor)
                ->filter(function ($row) {
                    return (int) $row->rank === 1;
                })
                ->map(function ($row) {
                    return (int) $row->id;
                })
                ->values()
                ->all();

            if (!empty($toDemoteIds)) {
                ItemVendor::whereIn('id', $toDemoteIds)->update(['rank' => 2]);
            }
        });

        return response()->json('Updated');
    }

    public function destroy(string $id)
    {
        if ($response = $this->ensureHrAccess()) {
            return $response;
        }

        $itemVendor = ItemVendor::findOrFail($id);
        $itemVendor->delete();

        return response()->json('Deleted');
    }
}


