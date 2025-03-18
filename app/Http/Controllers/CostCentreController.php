<?php

namespace App\Http\Controllers;

use App\Models\CostCentre;
use Illuminate\Http\Request;

class CostCentreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan semua cost centre berdasarkan perumahan pengguna
    public function index()
    {
        $user = auth()->user();
        $costCentres = CostCentre::where('perumahan_id', $user->perumahan_id)->get();

        if ($costCentres->isEmpty()) {
            return response()->json(['message' => 'Tidak ada Cost Centre ditemukan'], 404);
        }

        return response()->json($costCentres);
    }

    // Menampilkan detail cost centre tertentu
    public function show($id)
    {
        $user = auth()->user();
        $costCentre = CostCentre::where('id', $id)->where('perumahan_id', $user->perumahan_id)->first();

        if (!$costCentre) {
            return response()->json(['error' => 'Cost Centre tidak ditemukan atau tidak ada akses'], 404);
        }

        return response()->json($costCentre);
    }

    // Menyimpan cost centre baru
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'cost_centre_code' => 'required|string|unique:cost_centres,cost_centre_code',
            'description' => 'required|string'
        ]);

        $validated['perumahan_id'] = $user->perumahan_id;

        $costCentre = CostCentre::create($validated);

        return response()->json([
            'message' => 'Cost Centre berhasil ditambahkan',
            'data' => $costCentre
        ], 201);
    }

    // Memperbarui cost centre
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $costCentre = CostCentre::where('id', $id)->where('perumahan_id', $user->perumahan_id)->first();

        if (!$costCentre) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk mengupdate data ini.'], 403);
        }

        $validated = $request->validate([
            'cost_centre_code' => 'required|string|unique:cost_centres,cost_centre_code,' . $id,
            'description' => 'required|string'
        ]);

        $costCentre->update($validated);

        return response()->json([
            'message' => 'Cost Centre berhasil diupdate',
            'data' => $costCentre
        ], 200);
    }

    // Menghapus cost centre
    public function destroy($id)
    {
        $user = auth()->user();
        $costCentre = CostCentre::where('id', $id)->where('perumahan_id', $user->perumahan_id)->first();

        if (!$costCentre) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk menghapus data ini.'], 403);
        }

        $costCentre->delete();

        return response()->json([
            'message' => 'Cost Centre berhasil dihapus'
        ], 204);
    }
}
