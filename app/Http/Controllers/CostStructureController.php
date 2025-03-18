<?php

namespace App\Http\Controllers;

use App\Models\CostStructure;
use App\Models\CostTee;
use Illuminate\Http\Request;

class CostStructureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan semua cost structure berdasarkan perumahan pengguna
    public function index()
    {
        $user = auth()->user();
        $costStructures = CostStructure::whereHas('costTee', function ($query) use ($user) {
            $query->where('perumahan_id', $user->perumahan_id);
        })->with('costTee')->get();

        if ($costStructures->isEmpty()) {
            return response()->json(['message' => 'Tidak ada Cost Structure ditemukan'], 404);
        }

        return response()->json($costStructures);
    }

    // Menampilkan detail cost structure tertentu
    public function show($id)
    {
        $user = auth()->user();
        $costStructure = CostStructure::where('id', $id)
            ->whereHas('costTee', function ($query) use ($user) {
                $query->where('perumahan_id', $user->perumahan_id);
            })
            ->with('costTee')
            ->first();

        if (!$costStructure) {
            return response()->json(['error' => 'Cost Structure tidak ditemukan atau tidak ada akses'], 404);
        }

        return response()->json($costStructure);
    }

    // Menyimpan cost structure baru
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'cost_tee_code' => 'required|string|exists:cost_tees,cost_tee_code',
            'cost_code' => 'required|string|in:KASIN,KASOUT',
            'description' => 'required|string'
        ]);

        // Cek apakah cost_tee_code milik perumahan pengguna
        $costTee = CostTee::where('cost_tee_code', $validated['cost_tee_code'])
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costTee) {
            return response()->json(['error' => 'Unauthorized: Cost Tee tidak ditemukan atau bukan milik perumahan Anda.'], 403);
        }

        $validated['perumahan_id'] = $user->perumahan_id;

        $costStructure = CostStructure::create($validated);

        return response()->json([
            'message' => 'Cost Structure berhasil ditambahkan',
            'data' => $costStructure
        ], 201);
    }

    // Memperbarui cost structure
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $costStructure = CostStructure::where('id', $id)
            ->whereHas('costTee', function ($query) use ($user) {
                $query->where('perumahan_id', $user->perumahan_id);
            })
            ->first();

        if (!$costStructure) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk mengupdate data ini.'], 403);
        }

        $validated = $request->validate([
            'cost_tee_code' => 'required|string|exists:cost_tees,cost_tee_code',
            'cost_code' => 'required|string|in:KASIN,KASOUT',
            'description' => 'required|string'
        ]);

        // Cek apakah cost_tee_code milik perumahan pengguna
        $costTee = CostTee::where('cost_tee_code', $validated['cost_tee_code'])
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costTee) {
            return response()->json(['error' => 'Unauthorized: Cost Tee tidak ditemukan atau bukan milik perumahan Anda.'], 403);
        }

        $costStructure->update($validated);

        return response()->json([
            'message' => 'Cost Structure berhasil diupdate',
            'data' => $costStructure
        ], 200);
    }

    // Menghapus cost structure
    public function destroy($id)
    {
        $user = auth()->user();
        $costStructure = CostStructure::where('id', $id)
            ->whereHas('costTee', function ($query) use ($user) {
                $query->where('perumahan_id', $user->perumahan_id);
            })
            ->first();

        if (!$costStructure) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk menghapus data ini.'], 403);
        }

        $costStructure->delete();

        return response()->json([
            'message' => 'Cost Structure berhasil dihapus'
        ], 204);
    }
}
