<?php

namespace App\Http\Controllers;

use App\Models\CostCentre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CostCentreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $costCentres = CostCentre::where('perumahan_id', $user->perumahan_id)->get();

        if ($costCentres->isEmpty()) {
            return response()->json(['message' => 'Tidak ada Cost Centre ditemukan'], 404);
        }

        return response()->json($costCentres);
    }

    public function show($id)
    {
        $user = auth()->user();
        $costCentre = CostCentre::where('id', $id)
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costCentre) {
            return response()->json(['error' => 'Cost Centre tidak ditemukan atau tidak ada akses'], 404);
        }

        return response()->json($costCentre);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'cost_centre_code' => [
                'required',
                'string',
                Rule::unique('cost_centres')->where(fn($q) => $q->where('perumahan_id', $user->perumahan_id))
            ],
            'description' => 'required|string',
            'cost_code' => 'required|string|in:KASIN,KASOUT',
        ]);
        
        $validated['perumahan_id'] = $user->perumahan_id;
        
        $costCentre = CostCentre::create($validated);
        

        return response()->json([
            'message' => 'Cost Centre berhasil ditambahkan',
            'data' => $costCentre
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $costCentre = CostCentre::where('id', $id)
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costCentre) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk mengupdate data ini.'], 403);
        }

        $validated = $request->validate([
            'cost_centre_code' => [
                'required',
                'string',
                Rule::unique('cost_centres')->where(fn($q) => $q->where('perumahan_id', $user->perumahan_id))->ignore($id),
            ],
            'description' => 'required|string',
            'cost_code' => 'required|string|in:KASIN,KASOUT',
        ]);
        
        $costCentre->update($validated);
        

        return response()->json([
            'message' => 'Cost Centre berhasil diupdate',
            'data' => $costCentre
        ], 200);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $costCentre = CostCentre::where('id', $id)
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costCentre) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk menghapus data ini.'], 403);
        }

        $costCentre->delete();

        return response()->json([
            'message' => 'Cost Centre berhasil dihapus'
        ], 204);
    }
}
