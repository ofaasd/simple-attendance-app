<?php

namespace App\Http\Controllers;

use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\Sppg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashFlowController extends Controller
{
    public function index()
    {
        $title = 'Cashflow';
        $tableUrl = url('cashflow/get_table');

        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $employeeSppgIds = $isEmployee ? Auth::user()->sppgs->pluck('id')->all() : [];
        $sppg = $isEmployee && count($employeeSppgIds)
            ? Sppg::whereIn('id', $employeeSppgIds)->orderBy('nama')->get()
            : Sppg::orderBy('nama')->get();
        $disableSppgFilter = $isEmployee && count($employeeSppgIds) > 0;

        return view('cashflow.index', compact('title', 'tableUrl', 'sppg', 'isEmployee', 'disableSppgFilter'));
    }

    public function get_table(Request $request)
    {
        $filterSppgId = $request->input('filter_sppg_id');
        $filterTanggalStart = $request->input('filter_tanggal_start');
        $filterTanggalEnd = $request->input('filter_tanggal_end');
        $errorMessage = null;

        if ($filterTanggalStart && $filterTanggalEnd) {
            try {
                $start = new \DateTime($filterTanggalStart);
                $end = new \DateTime($filterTanggalEnd);
                $diffDays = (int) $start->diff($end)->days;
                if ($diffDays > 31) {
                    $errorMessage = 'Rentang tanggal maksimal 1 bulan. Silakan pilih rentang yang lebih pendek.';
                }
            } catch (\Exception $exception) {
                $errorMessage = 'Format tanggal tidak valid.';
            }
        }

        $employeeSppgIds = Auth::user()->hasRole('perwakilan yayasan') ? Auth::user()->sppgs->pluck('id')->all() : [];

        $cashInQuery = CashIn::with('sppg')
            ->when($employeeSppgIds, function ($q) use ($employeeSppgIds) {
                $q->whereIn('sppg_id', $employeeSppgIds);
            })
            ->when($filterSppgId, function ($q) use ($filterSppgId, $employeeSppgIds) {
                if (empty($employeeSppgIds) || in_array($filterSppgId, $employeeSppgIds)) {
                    $q->where('sppg_id', $filterSppgId);
                }
            })
            ->when($filterTanggalStart, function ($q) use ($filterTanggalStart) {
                $q->whereDate('tanggal', '>=', $filterTanggalStart);
            })
            ->when($filterTanggalEnd, function ($q) use ($filterTanggalEnd) {
                $q->whereDate('tanggal', '<=', $filterTanggalEnd);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        $cashOutQuery = CashOut::with(['sppg', 'jenisCashout'])
            ->when($employeeSppgIds, function ($q) use ($employeeSppgIds) {
                $q->whereIn('sppg_id', $employeeSppgIds);
            })
            ->when($filterSppgId, function ($q) use ($filterSppgId, $employeeSppgIds) {
                if (empty($employeeSppgIds) || in_array($filterSppgId, $employeeSppgIds)) {
                    $q->where('sppg_id', $filterSppgId);
                }
            })
            ->when($filterTanggalStart, function ($q) use ($filterTanggalStart) {
                $q->whereDate('tanggal', '>=', $filterTanggalStart);
            })
            ->when($filterTanggalEnd, function ($q) use ($filterTanggalEnd) {
                $q->whereDate('tanggal', '<=', $filterTanggalEnd);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        $cashIns = $cashInQuery->get();
        $cashOuts = $cashOutQuery->get();
        $totalCashIn = $cashIns->sum('jumlah_dana');
        $totalCashOut = $cashOuts->sum('nominal');
        $netCash = $totalCashIn - $totalCashOut;
        $no = 0;

        return view('cashflow.table', compact(
            'cashIns',
            'cashOuts',
            'totalCashIn',
            'totalCashOut',
            'netCash',
            'no',
            'errorMessage'
        ));
    }
}

