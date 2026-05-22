<?php

namespace App\Http\Controllers;

use App\Models\DistribusiMenu;
use App\Models\DistribusiDetail;
use App\Models\Menu;
use App\Models\PenerimaManfaat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Sppg;

class DistribusiMenuController extends Controller
{
    public function index()
    {
        $title = 'Distribusi Menu';
        
        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppgList = $isEmployee ? Sppg::where('user_id', Auth::id())->orderBy('nama')->get() : Sppg::orderBy('nama')->get();

        return view('distribusi_menu.index', compact('title', 'sppgList'));
    }

    public function get_table(Request $request)
    {
        $query = DistribusiMenu::with(['menu.sppg', 'distribusiDetails.penerimaManfaat']);

        if (Auth::user()->hasRole('perwakilan yayasan')) {
            $query->whereHas('menu.sppg', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        if ($request->filled('filter_sppg_id')) {
            $query->whereHas('menu', function($q) use ($request) {
                $q->where('sppg_id', $request->filter_sppg_id);
            });
        }

        if ($request->filled('filter_tanggal_start')) {
            $query->whereDate('tanggal_pengiriman', '>=', $request->filter_tanggal_start);
        }

        if ($request->filled('filter_tanggal_end')) {
            $query->whereDate('tanggal_pengiriman', '<=', $request->filter_tanggal_end);
        }

        $distribusi = $query->orderBy('tanggal_pengiriman', 'desc')->get();
        $no = 0;

        return view('distribusi_menu.table', compact('distribusi', 'no'));
    }

    public function create()
    {
        $title = 'Tambah Distribusi Menu';
        
        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppgQuery = $isEmployee ? Sppg::where('user_id', Auth::id()) : Sppg::query();
        $sppgIds = $sppgQuery->pluck('id');

        $menus = Menu::whereIn('sppg_id', $sppgIds)
                     ->with('sppg')
                     ->orderBy('tanggal', 'desc')
                     ->get();

        $penerimaManfaatSekolah = PenerimaManfaat::where('kategori', 'Sekolah')->where('sppg_id', $sppgIds)->orderBy('nama')->get();
        $penerimaManfaatB3 = PenerimaManfaat::where('kategori', 'B3')->where('sppg_id', $sppgIds)->orderBy('nama')->get();
        $penerimaManfaatLainnya = PenerimaManfaat::whereNotIn('kategori', ['Sekolah', 'B3'])->where('sppg_id', $sppgIds)->orWhereNull('kategori')->orderBy('nama')->get();

        return view('distribusi_menu.create', compact('title', 'menus', 'penerimaManfaatSekolah', 'penerimaManfaatB3', 'penerimaManfaatLainnya'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_menu' => 'required|exists:menu,id',
            'tanggal_pengiriman' => 'required|date',
            'tanggal_diterima' => 'nullable|date',
            'jumlah' => 'required|integer|min:0',
            'foto_menu' => 'nullable|image|max:5120',
            'foto_suhu' => 'nullable|image|max:5120',
            'status' => 'required|in:on progress,on delivery,done',
            'details' => 'required|array',
        ]);

        $data = $request->except(['foto_menu', 'foto_suhu', 'details']);

        if ($request->hasFile('foto_menu')) {
            $data['foto_menu'] = $request->file('foto_menu')->store('distribusi/foto_menu', 'public');
        }

        if ($request->hasFile('foto_suhu')) {
            $data['foto_suhu'] = $request->file('foto_suhu')->store('distribusi/foto_suhu', 'public');
        }

        $distribusi = DistribusiMenu::create($data);

        foreach ($request->details as $pmId => $detail) {
            $hasValue = false;
            foreach ($detail as $key => $val) {
                if ((int)$val > 0) {
                    $hasValue = true;
                    break;
                }
            }

            if ($hasValue) {
                $detailData = array_merge([
                    'id_distribusi' => $distribusi->id,
                    'id_penerima_manfaat' => $pmId,
                ], $detail);
                DistribusiDetail::create($detailData);
            }
        }

        return redirect()->route('distribusi_menu.index')->with('success', 'Distribusi menu berhasil disimpan.');
    }

    public function edit(DistribusiMenu $distribusiMenu)
    {
        $title = 'Edit Distribusi Menu';
        
        $isEmployee = Auth::user()->hasRole('perwakilan yayasan');
        $sppgQuery = $isEmployee ? Sppg::where('user_id', Auth::id()) : Sppg::query();
        $sppgIds = $sppgQuery->pluck('id');

        $menus = Menu::whereIn('sppg_id', $sppgIds)
                     ->with('sppg')
                     ->orderBy('tanggal', 'desc')
                     ->get();

        $penerimaManfaatSekolah = PenerimaManfaat::where('kategori', 'Sekolah')->where('sppg_id', $sppgIds)->orderBy('nama')->get();
        $penerimaManfaatB3 = PenerimaManfaat::where('kategori', 'B3')->where('sppg_id', $sppgIds)->orderBy('nama')->get();
        $penerimaManfaatLainnya = PenerimaManfaat::whereNotIn('kategori', ['Sekolah', 'B3'])->where('sppg_id', $sppgIds)->orWhereNull('kategori')->orderBy('nama')->get();

        $distribusiDetails = $distribusiMenu->distribusiDetails->keyBy('id_penerima_manfaat');

        return view('distribusi_menu.edit', compact('title', 'distribusiMenu', 'menus', 'penerimaManfaatSekolah', 'penerimaManfaatB3', 'penerimaManfaatLainnya', 'distribusiDetails'));
    }

    public function update(Request $request, DistribusiMenu $distribusiMenu)
    {
        $request->validate([
            'id_menu' => 'required|exists:menu,id',
            'tanggal_pengiriman' => 'required|date',
            'tanggal_diterima' => 'nullable|date',
            'jumlah' => 'required|integer|min:0',
            'foto_menu' => 'nullable|image|max:5120',
            'foto_suhu' => 'nullable|image|max:5120',
            'status' => 'required|in:on progress,on delivery,done',
            'details' => 'required|array',
        ]);

        $data = $request->except(['foto_menu', 'foto_suhu', 'details']);

        if ($request->hasFile('foto_menu')) {
            if ($distribusiMenu->foto_menu) {
                Storage::disk('public')->delete($distribusiMenu->foto_menu);
            }
            $data['foto_menu'] = $request->file('foto_menu')->store('distribusi/foto_menu', 'public');
        }

        if ($request->hasFile('foto_suhu')) {
            if ($distribusiMenu->foto_suhu) {
                Storage::disk('public')->delete($distribusiMenu->foto_suhu);
            }
            $data['foto_suhu'] = $request->file('foto_suhu')->store('distribusi/foto_suhu', 'public');
        }

        $distribusiMenu->update($data);

        // Sync details
        $distribusiMenu->distribusiDetails()->delete();
        foreach ($request->details as $pmId => $detail) {
            $hasValue = false;
            foreach ($detail as $key => $val) {
                if ((int)$val > 0) {
                    $hasValue = true;
                    break;
                }
            }

            if ($hasValue) {
                $detailData = array_merge([
                    'id_distribusi' => $distribusiMenu->id,
                    'id_penerima_manfaat' => $pmId,
                ], $detail);
                DistribusiDetail::create($detailData);
            }
        }

        return redirect()->route('distribusi_menu.index')->with('success', 'Distribusi menu berhasil diperbarui.');
    }

    public function destroy(DistribusiMenu $distribusiMenu)
    {
        if ($distribusiMenu->foto_menu) {
            Storage::disk('public')->delete($distribusiMenu->foto_menu);
        }
        if ($distribusiMenu->foto_suhu) {
            Storage::disk('public')->delete($distribusiMenu->foto_suhu);
        }

        $distribusiMenu->delete();
        return redirect()->route('distribusi_menu.index')->with('success', 'Distribusi menu berhasil dihapus.');
    }
}

