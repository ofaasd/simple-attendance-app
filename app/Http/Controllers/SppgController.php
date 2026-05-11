<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sppg;
use App\Models\User;

class SppgController extends Controller
{
    public function index()
    {
        $title = "SPPG Management";
        $users = User::orderBy('name')->get();
        $approverUsers = User::role(['akuntan', 'verval', 'head'])->orderBy('name')->get();

        return view('sppg.index', compact('title', 'users', 'approverUsers'));
    }

    public function get_table()
    {
        $sppg = Sppg::with(['user', 'users'])->get();
        $no = 0;
        return view('sppg.table', compact('sppg', 'no'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'alamat'   => 'required|string',
            'user_id'  => 'required|exists:users,id',
            'location' => 'nullable|url|max:5000',
            'lat'      => 'nullable|numeric|between:-90,90',
            'lng'      => 'nullable|numeric|between:-180,180',
            'approver_user_ids' => 'nullable|array',
            'approver_user_ids.*' => 'integer|exists:users,id',
        ]);

        $id = $request->id;
        $approverUserIds = collect($request->input('approver_user_ids', []))
            ->map(fn ($userId) => (int) $userId)
            ->unique()
            ->values();

        if ($approverUserIds->isNotEmpty()) {
            $validApproverCount = User::whereIn('id', $approverUserIds->all())
                ->role(['akuntan', 'verval', 'head'])
                ->count();

            if ($validApproverCount !== $approverUserIds->count()) {
                return response()->json(['message' => 'Daftar approver tidak valid. Pilih user dengan role akuntan/verval/head.'], 422);
            }
        }

        $sppg = null;

        \DB::transaction(function () use ($id, $request, $approverUserIds, &$sppg) {
            $sppg = Sppg::updateOrCreate(
                ['id' => $id],
                [
                    'nama'     => $request->nama,
                    'alamat'   => $request->alamat,
                    'location' => $request->location,
                    'lat'      => $request->lat,
                    'lng'      => $request->lng,
                    'user_id'  => $request->user_id,
                ]
            );

            $sppg->users()->sync($approverUserIds->all());
        });

        return response()->json($id ? 'Updated' : 'Saved');
    }

    public function edit(string $id)
    {
        $sppg = Sppg::with('users:id')->findOrFail($id);
        $sppg->approver_user_ids = $sppg->users->pluck('id')->map(fn ($userId) => (int) $userId)->values();

        return response()->json([$sppg]);
    }

    public function destroy(string $id)
    {
        Sppg::findOrFail($id)->delete();
        return response()->json('Deleted');
    }
}

