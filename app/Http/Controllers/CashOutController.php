<?php

namespace App\Http\Controllers;

use App\Models\CashOut;
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

        return view('cash_out.index', compact('title', 'tableUrl', 'sppg'));
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
}

