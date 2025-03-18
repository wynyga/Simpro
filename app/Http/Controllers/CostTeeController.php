<?php

namespace App\Http\Controllers;

use App\Models\CostTee;
use App\Models\CostElement;
use Illuminate\Http\Request;

class CostTeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan semua cost tee berdasarkan perumahan pengguna
    public function index()
    {
        $user = auth()->user();
        $costTees = CostTee::whereHas('costElement', function ($query) use ($user) {
            $query->where('perumahan_id', $user->perumahan_id);
        })->with('costElement')->get();

        if ($costTees->isEmpty()) {
            return response()->json(['message' => 'Tidak ada Cost Tee ditemukan'], 404);
        }

        return response()->json($costTees);
    }

    // Menampilkan detail cost tee tertentu
    public function show($id)
    {
        $user = auth()->user();
        $costTee = CostTee::where('id', $id)
            ->whereHas('costElement', function ($query) use ($user) {
                $query->where('perumahan_id', $user->perumahan_id);
            })
            ->with('costElement')
            ->first();

        if (!$costTee) {
            return response()->json(['error' => 'Cost Tee tidak ditemukan atau tidak ada akses'], 404);
        }

        return response()->json($costTee);
    }

    // Menyimpan cost tee baru
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'cost_tee_code' => 'required|string|unique:cost_tees,cost_tee_code',
            'cost_element_code' => 'required|string|exists:cost_elements,cost_element_code',
            'description' => 'required|string'
        ]);

        // Cek apakah cost_element_code milik perumahan pengguna
        $costElement = CostElement::where('cost_element_code', $validated['cost_element_code'])
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costElement) {
            return response()->json(['error' => 'Unauthorized: Cost Element tidak ditemukan atau bukan milik perumahan Anda.'], 403);
        }

        $validated['perumahan_id'] = $user->perumahan_id;

        $costTee = CostTee::create($validated);

        return response()->json([
            'message' => 'Cost Tee berhasil ditambahkan',
            'data' => $costTee
        ], 201);
    }

    // Memperbarui cost tee
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $costTee = CostTee::where('id', $id)
            ->whereHas('costElement', function ($query) use ($user) {
                $query->where('perumahan_id', $user->perumahan_id);
            })
            ->first();

        if (!$costTee) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk mengupdate data ini.'], 403);
        }

        $validated = $request->validate([
            'cost_tee_code' => 'required|string|unique:cost_tees,cost_tee_code,' . $id,
            'cost_element_code' => 'required|string|exists:cost_elements,cost_element_code',
            'description' => 'required|string'
        ]);

        // Cek apakah cost_element_code milik perumahan pengguna
        $costElement = CostElement::where('cost_element_code', $validated['cost_element_code'])
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costElement) {
            return response()->json(['error' => 'Unauthorized: Cost Element tidak ditemukan atau bukan milik perumahan Anda.'], 403);
        }

        $costTee->update($validated);

        return response()->json([
            'message' => 'Cost Tee berhasil diupdate',
            'data' => $costTee
        ], 200);
    }

    // Menghapus cost tee
    public function destroy($id)
    {
        $user = auth()->user();
        $costTee = CostTee::where('id', $id)
            ->whereHas('costElement', function ($query) use ($user) {
                $query->where('perumahan_id', $user->perumahan_id);
            })
            ->first();

        if (!$costTee) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk menghapus data ini.'], 403);
        }

        $costTee->delete();

        return response()->json([
            'message' => 'Cost Tee berhasil dihapus'
        ], 204);
    }
}
