<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Models\Sppg;

class VendorController extends Controller
{
    public function index()
    {
        $title = 'Vendor Management';
        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppg = $isEmployee
            ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get()
            : Sppg::orderBy('nama')->get();
        return view('vendor.index', compact('title', 'sppg', 'isEmployee'));
    }

    public function get_table()
    {
        $vendor = Vendor::with('sppg')
            ->when(Auth::user()->hasRole('perwakilan yayasan'), function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->orderBy('id', 'desc')
            ->get();
        $no = 0;
        return view('vendor.table', compact('vendor', 'no'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_vendor' => ['required', 'string', 'max:50', Rule::unique('vendor', 'kode_vendor')->ignore($request->id)],
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'pic_nama' => 'nullable|string|max:255',
            'pic_jabatan' => 'nullable|string|max:255',
            'pic_no_telp' => 'nullable|string|max:50',
            'termin_pembayaran' => 'nullable|string|max:100',
            'metode_pengiriman' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'status' => 'nullable|in:aktif,tidak aktif',
            'sppg_id' => 'required|exists:sppg,id',
        ]);

        Vendor::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_vendor' => $request->kode_vendor,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'pic_nama' => $request->pic_nama,
                'pic_jabatan' => $request->pic_jabatan,
                'pic_no_telp' => $request->pic_no_telp,
                'termin_pembayaran' => $request->termin_pembayaran,
                'metode_pengiriman' => $request->metode_pengiriman,
                'catatan' => $request->catatan,
                'sppg_id' => $request->sppg_id,
                'status' => $request->status ?: 'aktif',
            ]
        );

        return response()->json('Saved');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'sppg_id' => 'required|exists:sppg,id',
        ]);
        $sppg_id = $request->sppg_id;
        $rows = Excel::toArray([], $request->file('file'));
        $sheet = $rows[0] ?? [];

        if (count($sheet) === 0) {
            return response()->json(['message' => 'File kosong, tidak ada data yang dapat diimport.'], 422);
        }

        // Urutan kolom sesuai permintaan:
        // 0: kode_vendor, 1: nama, 2: alamat, 3: no_telp, 4: email,
        // 5: pic_nama, 6: pic_jabatan, 7: pic_no_telp,
        // 8: termin_pembayaran, 9: metode_pengiriman, 10: catatan, 11: status
        $headerMap = [
            'kode'          => 0, 'kode vendor'   => 0, 'kode_vendor'   => 0,
            'nama'          => 1, 'nama vendor'   => 1,
            'alamat'        => 2,
            'no telp'       => 3, 'no_telp'       => 3, 'telepon'       => 3, 'phone' => 3,
            'email'         => 4,
            'pic nama'      => 5, 'pic_nama'      => 5, 'nama pic'      => 5,
            'pic jabatan'   => 6, 'pic_jabatan'   => 6, 'jabatan pic'   => 6,
            'pic no telp'   => 7, 'pic_no_telp'   => 7, 'telp pic'      => 7,
            'termin'        => 8, 'termin pembayaran' => 8, 'termin_pembayaran' => 8,
            'metode'        => 9, 'metode pengiriman' => 9, 'metode_pengiriman' => 9,
            'catatan'       => 10, 'notes'         => 10,
            'status'        => 11,
        ];

        $firstRow = $sheet[0] ?? [];
        $normalizedHeaders = array_map(fn ($h) => strtolower(trim((string) ($h ?? ''))), $firstRow);

        $hasHeader = false;
        foreach ($normalizedHeaders as $h) {
            if (isset($headerMap[$h])) {
                $hasHeader = true;
                break;
            }
        }

        $colIndex = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]; // default urutan

        if ($hasHeader) {
            $detected = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
            foreach ($normalizedHeaders as $pos => $h) {
                if (isset($headerMap[$h])) {
                    $detected[$headerMap[$h]] = $pos;
                }
            }
            $colIndex = $detected;
        }

        $imported = 0;
        $skipped  = 0;
        $failed   = [];
        $startIndex = $hasHeader ? 1 : 0;

        // Tambahkan $sppg_id di dalam use()
        DB::transaction(function () use ($sheet, $startIndex, $colIndex, &$imported, &$skipped, &$failed, $sppg_id) {
            for ($i = $startIndex; $i < count($sheet); $i++) {
                $row        = $sheet[$i];
                $lineNumber = $i + 1;

                $kode = trim((string) ($row[$colIndex[0]] ?? ''));
                $nama = trim((string) ($row[$colIndex[1]] ?? ''));

                if ($kode === '' && $nama === '') {
                    continue;
                }

                if ($kode === '' || $nama === '') {
                    $failed[] = ['row' => $lineNumber, 'reason' => 'Kode Vendor dan Nama wajib diisi.'];
                    continue;
                }

                $status = strtolower(trim((string) ($row[$colIndex[11]] ?? '')));
                if (!in_array($status, ['aktif', 'tidak aktif'], true)) {
                    $status = 'aktif';
                }

                $exists = Vendor::where('kode_vendor', $kode)->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }

                Vendor::create([
                    'kode_vendor'       => $kode,
                    'nama'              => $nama,
                    'alamat'            => trim((string) ($row[$colIndex[2]] ?? '')) ?: null,
                    'no_telp'           => trim((string) ($row[$colIndex[3]] ?? '')) ?: null,
                    'email'             => trim((string) ($row[$colIndex[4]] ?? '')) ?: null,
                    'pic_nama'          => trim((string) ($row[$colIndex[5]] ?? '')) ?: null,
                    'pic_jabatan'       => trim((string) ($row[$colIndex[6]] ?? '')) ?: null,
                    'pic_no_telp'       => trim((string) ($row[$colIndex[7]] ?? '')) ?: null,
                    'termin_pembayaran' => trim((string) ($row[$colIndex[8]] ?? '')) ?: null,
                    'metode_pengiriman' => trim((string) ($row[$colIndex[9]] ?? '')) ?: null,
                    'catatan'           => trim((string) ($row[$colIndex[10]] ?? '')) ?: null,
                    'status'            => $status,
                    'sppg_id'           => $sppg_id, // Sekarang $sppg_id sudah terbaca di sini
                ]);

                $imported++;
            }
        });

        $msg = 'Import selesai. Berhasil: ' . $imported . ' data.'
            . ($skipped ? ' Skip (kode duplikat): ' . $skipped . ' data.' : '')
            . (count($failed) ? ' Gagal: ' . count($failed) . ' data.' : '');

        return response()->json([
            'message'      => $msg,
            'imported'     => $imported,
            'skipped'      => $skipped,
            'failed_count' => count($failed),
            'failed_rows'  => array_slice($failed, 0, 20),
        ]);
    }

    public function edit(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->json([$vendor]);
    }

    public function destroy(string $id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($vendor->itemPrices()->exists()) {
            return response()->json([
                'message' => 'Vendor tidak dapat dihapus karena sudah memiliki relasi dengan data item vendor.'
            ], 422);
        }

        $vendor->delete();

        return response()->json('Deleted');
    }
}

