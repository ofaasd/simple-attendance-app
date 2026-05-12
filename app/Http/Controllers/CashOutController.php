<?php

namespace App\Http\Controllers;

use App\Models\CashOut;
use App\Models\JenisCashout;
use App\Models\Sppg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashOutController extends Controller
{
    public function index()
    {
        $title = 'Cash Out';
        $tableUrl = url('cash-out/get_table');

        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppg = $isEmployee ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get() : Sppg::orderBy('nama')->get();
        $jenisCashouts = JenisCashout::orderBy('nama')->get();

        return view('cash_out.index', compact('title', 'tableUrl', 'sppg', 'jenisCashouts'));
    }

    public function get_table(Request $request)
    {
        $query = CashOut::with(['sppg', 'jenisCashout'])
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

        $cashOuts = $query->get();
        $no = 0;

        return view('cash_out.table', compact('cashOuts', 'no'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('perwakilan yayasan') && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'sppg_id' => 'required|exists:sppg,id',
            'jenis_cashout_id' => 'required|exists:jenis_cashout,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);
        

        if(CashOut::create($validated)){
            $sppg = Sppg::find($validated['sppg_id']);
            $sppg->saldo -= $validated['nominal'];
            $sppg->save();
        }

        return redirect()->route('cash_out')->with('success', 'Cash out berhasil ditambahkan. Harap periksa kembali data yang dimasukkan.');
    }
}

