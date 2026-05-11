<?php

namespace App\Http\Controllers;

use App\Models\PenerimaManfaat;
use App\Imports\PenerimaManfaatImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PenerimaManfaatController extends Controller
{
    public function index()
    {
        $title = 'Penerima Manfaat';
        $penerimaManfaat = PenerimaManfaat::all();
        return view('penerima_manfaat.index', compact('title', 'penerimaManfaat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:50',
            'pic' => 'nullable|string|max:255',
        ]);

        PenerimaManfaat::create($request->all());

        return redirect()->route('penerima_manfaat.index')->with('success', 'Data penerima manfaat berhasil ditambahkan.');
    }

    public function update(Request $request, PenerimaManfaat $penerimaManfaat)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:50',
            'pic' => 'nullable|string|max:255',
        ]);

        $penerimaManfaat->update($request->all());

        return redirect()->route('penerima_manfaat.index')->with('success', 'Data penerima manfaat berhasil diperbarui.');
    }

    public function destroy(PenerimaManfaat $penerimaManfaat)
    {
        $penerimaManfaat->delete();

        return redirect()->route('penerima_manfaat.index')->with('success', 'Data penerima manfaat berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new PenerimaManfaatImport, $request->file('file'));

        return redirect()->route('penerima_manfaat.index')->with('success', 'Data penerima manfaat berhasil diimport.');
    }
}
