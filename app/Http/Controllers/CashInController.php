<?php

namespace App\Http\Controllers;

use App\Models\CashIn;
use App\Models\Sppg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashInController extends Controller
{
    public function index()
    {
        $title = 'Cash In';
        $tableUrl = url('cash-in/get_table');

        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppg = $isEmployee ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get() : Sppg::orderBy('nama')->get();

        return view('cash_in.index', compact('title', 'tableUrl', 'sppg'));
    }

    public function get_table(Request $request)
    {
        $query = CashIn::with('sppg')
            ->when(Auth::user()->hasRole('perwakilan yayasan'), function ($q) {
                $q->whereHas('sppg', function ($s) {
                    $s->where('user_id', Auth::id());
                });
            })
            ->when($request->filled('filter_sppg_id'), function ($q) use ($request) {
                $q->where('sppg_id', $request->filter_sppg_id);
            })
            ->when($request->filled('filter_tanggal_start'), function ($q) use ($request) {
                $q->whereDate('tanggal', '>=', $request->filter_tanggal_start);
            })
            ->when($request->filled('filter_tanggal_end'), function ($q) use ($request) {
                $q->whereDate('tanggal', '<=', $request->filter_tanggal_end);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        $cashIns = $query->get();
        $no = 0;

        return view('cash_in.table', compact('cashIns', 'no'));
    }

    public function create()
    {
        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppg = $isEmployee ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get() : Sppg::orderBy('nama')->get();

        return view('cash_in.create', compact('sppg'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'tanggal' => 'required|date',
            'sumber_dana' => 'required|string|max:255',
            'jumlah_dana' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $sppgId = (int) $request->sppg_id;
        if (Auth::user()->hasRole('perwakilan yayasan')) {
            $ownedSppg = Sppg::where('id', $sppgId)->where('user_id', Auth::id())->exists();
            if (!$ownedSppg) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke SPPG ini.'], 403);
            }
        }

        DB::transaction(function () use ($request, $sppgId) {
            $cashIn = CashIn::create([
                'sppg_id' => $sppgId,
                'tanggal' => $request->tanggal,
                'sumber_dana' => $request->sumber_dana,
                'jumlah_dana' => $request->jumlah_dana,
                'keterangan' => $request->keterangan,
            ]);

            // Update saldo SPPG
            $sppg = Sppg::find($sppgId);
            $sppg->increment('saldo', $request->jumlah_dana);
        });

        return redirect()->route('cash_in')->with('success', 'Cash In berhasil ditambahkan.');
    }

}

