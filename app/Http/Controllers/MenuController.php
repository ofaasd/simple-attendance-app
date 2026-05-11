<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Menu;
use App\Models\Sppg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    private function getMenuMasterData(): array
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
            ->orderBy('nama')
            ->get();

        return compact('isEmployee', 'sppg', 'items');
    }

    private function getMenuBaseQuery()
    {
        return Menu::with(['sppg', 'kategori', 'items', 'detailMenus'])
            ->when(Auth::user()->hasRole\('perwakilan\ yayasan'\), function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');
    }

    public function index()
    {
        $title = 'Menu';
        $tableUrl = url('menu-item/get_table');
        $addButtonLabel = 'Add Menu';

        $data = $this->getMenuMasterData();
        return view('menu.index', array_merge($data, compact('title', 'tableUrl', 'addButtonLabel')));
    }

    public function create()
    {
        $title = 'Add Menu';

        $data = $this->getMenuMasterData();
        return view('menu.create', array_merge($data, compact('title')));
    }

    public function get_table(Request $request)
    {
        $menu = $this->getMenuBaseQuery()
            ->when($request->filled('filter_tanggal_start'), function ($q) use ($request) {
                $q->whereDate('tanggal', '>=', $request->filter_tanggal_start);
            })
            ->when($request->filled('filter_tanggal_end'), function ($q) use ($request) {
                $q->whereDate('tanggal', '<=', $request->filter_tanggal_end);
            })
            ->get();
        $no = 0;
        return view('menu.table', compact('menu', 'no'));
    }

    public function getByDate(Request $request)
    {
        $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'tanggal' => 'required|date',
        ]);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $ownedSppg = Sppg::where('id', $request->sppg_id)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
            }
        }

        $menus = Menu::where('sppg_id', $request->sppg_id)
            ->whereDate('tanggal', $request->tanggal)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json(['menus' => $menus]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'sppg_id' => 'required|exists:sppg,id',
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'integer|exists:item,id',
            'detail_menu_names' => 'required|array|min:1',
            'detail_menu_names.*' => 'required|string|max:255',
        ]);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $ownedSppg = Sppg::where('id', $request->sppg_id)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
            }
        }

        $itemIds = collect($request->item_ids)->map(fn($id) => (int) $id)->unique()->values();
        $detailMenuNames = collect($request->detail_menu_names)
            ->map(fn($name) => trim((string) $name))
            ->filter(fn($name) => $name !== '')
            ->unique()
            ->values();

        if ($detailMenuNames->isEmpty()) {
            return response()->json(['message' => 'Detail menu wajib diisi minimal 1 data.'], 422);
        }

        $validItemCount = Item::whereIn('id', $itemIds)->where('sppg_id', $request->sppg_id)->count();
        if ($validItemCount !== $itemIds->count()) {
            return response()->json(['message' => 'Semua item pada menu harus berasal dari SPPG yang sama.'], 422);
        }

        DB::transaction(function () use ($request, $itemIds, $detailMenuNames) {
            $menu = Menu::updateOrCreate(
                ['id' => $request->id],
                [
                    'tanggal' => $request->tanggal,
                    'nama' => $request->nama,
                    'sppg_id' => $request->sppg_id,
                    'kategori_id' => null,
                    'uom_id' => null,
                ]
            );

            $menu->items()->sync($itemIds->all());

            $menu->detailMenus()->delete();
            $menu->detailMenus()->createMany(
                $detailMenuNames->map(function ($namaDetailMenu) {
                    return ['nama_detail_menu' => $namaDetailMenu];
                })->all()
            );
        });

        return response()->json('Saved');
    }

    public function copyFromDate(Request $request)
    {
        $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'source_tanggal' => 'required|date',
            'target_tanggal' => 'required|date|different:source_tanggal',
        ]);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $ownedSppg = Sppg::where('id', $request->sppg_id)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
            }
        }

        $sourceMenus = Menu::with(['items:id', 'detailMenus:id,id_menu,nama_detail_menu'])
            ->where('sppg_id', $request->sppg_id)
            ->whereDate('tanggal', $request->source_tanggal)
            ->get();

        if ($sourceMenus->isEmpty()) {
            return response()->json(['message' => 'Data menu pada tanggal sumber tidak ditemukan.'], 422);
        }

        $copiedCount = 0;

        DB::transaction(function () use ($request, $sourceMenus, &$copiedCount) {
            foreach ($sourceMenus as $sourceMenu) {
                $targetMenu = Menu::updateOrCreate(
                    [
                        'sppg_id' => $request->sppg_id,
                        'tanggal' => $request->target_tanggal,
                        'nama' => $sourceMenu->nama,
                    ],
                    []
                );

                $targetMenu->items()->sync($sourceMenu->items->pluck('id')->all());

                $targetMenu->detailMenus()->delete();
                $targetMenu->detailMenus()->createMany(
                    $sourceMenu->detailMenus->map(function ($detail) {
                        return ['nama_detail_menu' => $detail->nama_detail_menu];
                    })->all()
                );

                $copiedCount++;
            }
        });

        return response()->json([
            'message' => 'Berhasil menyalin menu.',
            'copied_count' => $copiedCount,
        ]);
    }

    public function edit(string $id)
    {
        $menu = Menu::with(['items:id', 'detailMenus:id,id_menu,nama_detail_menu'])->findOrFail($id);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\) && optional($menu->sppg)->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke data ini.'], 403);
        }

        return response()->json([
            'menu' => $menu,
            'item_ids' => $menu->items->pluck('id')->values(),
            'detail_menu_names' => $menu->detailMenus->pluck('nama_detail_menu')->values(),
        ]);
    }

    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\) && optional($menu->sppg)->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke data ini.'], 403);
        }

        $menu->delete();
        return response()->json('Deleted');
    }
}

