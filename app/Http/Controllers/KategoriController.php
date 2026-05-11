<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Sppg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    public function index()
    {
        $title = 'Kategori Management';
        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppg = $isEmployee
            ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get()
            : Sppg::orderBy('nama')->get();

        return view('kategori.index', compact('title', 'sppg', 'isEmployee'));
    }

    public function get_table()
    {
        $kategori = Kategori::with('sppg')
            ->when(Auth::user()->hasRole('perwakilan yayasan'), function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->orderBy('id', 'desc')
            ->get();
        $no = 0;
        return view('kategori.table', compact('kategori', 'no'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'nama' => 'required|string|max:255',
        ]);

        if (Auth::user()->hasRole('perwakilan yayasan')) {
            $ownedSppg = Sppg::where('id', $request->sppg_id)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
            }
        }

        Kategori::updateOrCreate(
            ['id' => $request->id],
            [
                'sppg_id' => $request->sppg_id,
                'nama' => $request->nama,
            ]
        );

        return response()->json('Saved');
    }

    public function edit(string $id)
    {
        $kategori = Kategori::findOrFail($id);

        if (Auth::user()->hasRole('perwakilan yayasan') && optional($kategori->sppg)->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke data ini.'], 403);
        }

        return response()->json([$kategori]);
    }

    public function destroy(string $id)
    {
        $kategori = Kategori::findOrFail($id);

        if (Auth::user()->hasRole('perwakilan yayasan') && optional($kategori->sppg)->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke data ini.'], 403);
        }

        $kategori->delete();
        return response()->json('Deleted');
    }
}

