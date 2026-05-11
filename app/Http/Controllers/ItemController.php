<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Sppg;
use App\Models\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    private function normalizeImportCell($value): string
    {
        return trim((string) ($value ?? ''));
    }

    private function normalizeImportHeader($value): string
    {
        return strtolower(trim((string) ($value ?? '')));
    }

    private function getItemMasterData(): array
    {
        $isEmployee = Auth::user()->hasRole\('perwakilan\ yayasan'\);
        $sppg = $isEmployee
            ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get()
            : Sppg::orderBy('nama')->get();

        $kategori = Kategori::when($isEmployee, function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->with('sppg')
            ->orderBy('nama')
            ->get();

        $uom = Uom::when($isEmployee, function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->orderBy('nama')
            ->get();

        return compact('isEmployee', 'sppg', 'kategori', 'uom');
    }

    private function getItemBaseQuery()
    {
        return Item::with(['sppg', 'kategori', 'uom'])
            ->when(Auth::user()->hasRole\('perwakilan\ yayasan'\), function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->orderBy('id', 'desc');
    }

    public function index()
    {
        $title = 'Item / Bahan Pokok';
        $tableUrl = url('item/get_table');
        $addButtonLabel = 'Add Item';

        $data = $this->getItemMasterData();
        return view('item.index', array_merge($data, compact('title', 'tableUrl', 'addButtonLabel')));
    }

    public function get_table()
    {
        $item = $this->getItemBaseQuery()->get();
        $no = 0;
        return view('item.table', compact('item', 'no'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'sppg_id' => 'required|exists:sppg,id',
            'kategori_id' => 'required|exists:kategori,id',
            'uom_id' => 'required|exists:uom,id',
        ]);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $ownedSppg = Sppg::where('id', $request->sppg_id)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
            }
        }

        $kategori = Kategori::findOrFail($request->kategori_id);
        $uom = Uom::findOrFail($request->uom_id);
        if ((int) $kategori->sppg_id !== (int) $request->sppg_id || (int) $uom->sppg_id !== (int) $request->sppg_id) {
            return response()->json(['message' => 'Kategori dan UOM harus berasal dari SPPG yang sama.'], 422);
        }

        DB::transaction(function () use ($request) {
            Item::updateOrCreate(
                ['id' => $request->id],
                [
                    'nama' => $request->nama,
                    'sppg_id' => $request->sppg_id,
                    'kategori_id' => $request->kategori_id,
                    'uom_id' => $request->uom_id,
                ]
            );
        });

        return response()->json('Saved');
    }

    public function import(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $kategori = Kategori::findOrFail($request->kategori_id);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\)) {
            $ownedSppg = Sppg::where('id', $kategori->sppg_id)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke kategori ini.'], 403);
            }
        }

        $rows = Excel::toArray([], $request->file('file'));
        $sheet = $rows[0] ?? [];

        if (count($sheet) === 0) {
            return response()->json(['message' => 'File kosong, tidak ada data yang dapat diimport.'], 422);
        }

        $firstRow = $sheet[0] ?? [];
        $normalizedHeaders = array_map(fn ($header) => $this->normalizeImportHeader($header), $firstRow);
        $hasHeader = in_array('nama', $normalizedHeaders, true)
            || in_array('item', $normalizedHeaders, true)
            || in_array('nama item', $normalizedHeaders, true)
            || in_array('uom', $normalizedHeaders, true)
            || in_array('satuan', $normalizedHeaders, true);

        $itemIndex = 0;
        $uomIndex = 1;

        if ($hasHeader) {
            foreach ($normalizedHeaders as $index => $header) {
                if (in_array($header, ['nama', 'item', 'nama item'], true)) {
                    $itemIndex = $index;
                }
                if (in_array($header, ['uom', 'satuan'], true)) {
                    $uomIndex = $index;
                }
            }
        }

        $imported = 0;
        $skipped = 0;
        $failed = [];
        $startIndex = $hasHeader ? 1 : 0;

        $uomCache = Uom::where('sppg_id', $kategori->sppg_id)
            ->get(['id', 'nama'])
            ->keyBy(function ($uom) {
                return strtolower(trim((string) $uom->nama));
            });

        DB::transaction(function () use ($sheet, $startIndex, $itemIndex, $uomIndex, $kategori, &$imported, &$skipped, &$failed, &$uomCache) {
            for ($rowIndex = $startIndex; $rowIndex < count($sheet); $rowIndex++) {
                $excelRow = $sheet[$rowIndex];
                $lineNumber = $rowIndex + 1;

                $itemName = $this->normalizeImportCell($excelRow[$itemIndex] ?? '');
                $uomName = $this->normalizeImportCell($excelRow[$uomIndex] ?? '');

                if ($itemName === '' && $uomName === '') {
                    continue;
                }

                if ($itemName === '' || $uomName === '') {
                    $failed[] = [
                        'row' => $lineNumber,
                        'reason' => 'Nama item dan UOM wajib diisi.',
                    ];
                    continue;
                }

                $uomKey = strtolower($uomName);
                $uom = $uomCache->get($uomKey);

                if (!$uom) {
                    $uom = Uom::create([
                        'sppg_id' => $kategori->sppg_id,
                        'nama' => $uomName,
                    ]);

                    $uomCache->put($uomKey, $uom);
                }

                $existsSameNameAndUom = Item::where('sppg_id', $kategori->sppg_id)
                    ->whereRaw('LOWER(nama) = ?', [strtolower($itemName)])
                    ->where('uom_id', $uom->id)
                    ->exists();

                if ($existsSameNameAndUom) {
                    $skipped++;
                    continue;
                }

                Item::create([
                    'sppg_id' => $kategori->sppg_id,
                    'nama' => $itemName,
                    'kategori_id' => $kategori->id,
                    'uom_id' => $uom->id,
                ]);

                $imported++;
            }
        });

        $response = [
            'message' => 'Import selesai. Berhasil: ' . $imported . ' data.' . ($skipped ? ' Skip: ' . $skipped . ' data.' : '') . (count($failed) ? ' Gagal: ' . count($failed) . ' data.' : ''),
            'imported' => $imported,
            'skipped' => $skipped,
            'failed_count' => count($failed),
            'failed_rows' => array_slice($failed, 0, 20),
        ];

        return response()->json($response);
    }

    public function edit(string $id)
    {
        $item = Item::findOrFail($id);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\) && optional($item->sppg)->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke data ini.'], 403);
        }

        return response()->json(['item' => $item]);
    }

    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);

        if (Auth::user()->hasRole\('perwakilan\ yayasan'\) && optional($item->sppg)->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke data ini.'], 403);
        }

        $item->delete();
        return response()->json('Deleted');
    }
}

