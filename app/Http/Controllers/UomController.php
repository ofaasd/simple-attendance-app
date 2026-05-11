<?php

namespace App\Http\Controllers;

use App\Models\Sppg;
use App\Models\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UomController extends Controller
{
    public function index()
    {
        $title = 'UOM Management';
        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppg = $isEmployee
            ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get()
            : Sppg::orderBy('nama')->get();

        return view('uom.index', compact('title', 'sppg', 'isEmployee'));
    }

    public function get_table()
    {
        $uom = Uom::with('sppg')
            ->when(Auth::user()->hasRole('perwakilan yayasan'), function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->orderBy('id', 'desc')
            ->get();
        $no = 0;
        return view('uom.table', compact('uom', 'no'));
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

        Uom::updateOrCreate(
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
        $uom = Uom::findOrFail($id);

        if (Auth::user()->hasRole('perwakilan yayasan') && optional($uom->sppg)->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke data ini.'], 403);
        }

        return response()->json([$uom]);
    }

    public function destroy(string $id)
    {
        $uom = Uom::findOrFail($id);

        if (Auth::user()->hasRole('perwakilan yayasan') && optional($uom->sppg)->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke data ini.'], 403);
        }

        $uom->delete();
        return response()->json('Deleted');
    }
}

