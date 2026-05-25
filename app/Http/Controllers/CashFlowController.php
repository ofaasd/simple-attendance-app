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
        $employeeSppgIds = $isEmployee ? Auth::user()->sppgs()->pluck('sppg.id')->all() : [];
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

        $employeeSppgIds = Auth::user()->hasRole('perwakilan yayasan') ? Auth::user()->sppgs()->pluck('sppg.id')->all() : [];

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

    public function downloadPdf(Request $request)
    {
        $filterSppgId = $request->input('filter_sppg_id');
        $filterTanggalStart = $request->input('filter_tanggal_start');
        $filterTanggalEnd = $request->input('filter_tanggal_end');

        if ($filterTanggalStart && $filterTanggalEnd) {
            try {
                $start = new \DateTime($filterTanggalStart);
                $end = new \DateTime($filterTanggalEnd);
                $diffDays = (int) $start->diff($end)->days;
                if ($diffDays > 31) {
                    abort(422, 'Rentang tanggal maksimal 1 bulan.');
                }
            } catch (\Exception $exception) {
                abort(422, 'Format tanggal tidak valid.');
            }
        }

        $employeeSppgIds = Auth::user()->hasRole('perwakilan yayasan') ? Auth::user()->sppgs()->pluck('sppg.id')->all() : [];

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

        $sppgName = 'Semua SPPG';
        if ($filterSppgId) {
            $sppgModel = Sppg::find($filterSppgId);
            if ($sppgModel) {
                $sppgName = $sppgModel->nama;
            }
        } elseif ($employeeSppgIds) {
            $sppgName = Sppg::whereIn('id', $employeeSppgIds)->pluck('nama')->implode(', ');
        }

        $periode = '-';
        if ($filterTanggalStart && $filterTanggalEnd) {
            $periode = date('d M Y', strtotime($filterTanggalStart)) . ' s/d ' . date('d M Y', strtotime($filterTanggalEnd));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cashflow.pdf', compact(
            'cashIns',
            'cashOuts',
            'totalCashIn',
            'totalCashOut',
            'netCash',
            'sppgName',
            'periode'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Cashflow_' . date('Ymd_His') . '.pdf');
    }
}

