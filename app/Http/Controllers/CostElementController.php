<?php

namespace App\Http\Controllers;

use App\Models\CostElement;
use App\Models\CostCentre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CostElementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan semua cost element berdasarkan perumahan pengguna
    public function index()
    {
        $user = auth()->user();
        $costElements = CostElement::where('perumahan_id', $user->perumahan_id)
            ->with('costCentre')
            ->get();
    
        if ($costElements->isEmpty()) {
            return response()->json(['message' => 'Tidak ada Cost Element ditemukan'], 404);
        }
    
        return response()->json($costElements);
    }
    

    // Menampilkan detail cost element tertentu
    public function show($id)
    {
        $user = auth()->user();
        $costElement = CostElement::where('id', $id)
            ->whereHas('costCentre', function ($query) use ($user) {
                $query->where('perumahan_id', $user->perumahan_id);
            })
            ->with('costCentre')
            ->first();

        if (!$costElement) {
            return response()->json(['error' => 'Cost Element tidak ditemukan atau tidak ada akses'], 404);
        }

        return response()->json($costElement);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'cost_element_code' => [
                'required',
                'string',
                Rule::unique('cost_elements')->where(fn($q) => $q->where('perumahan_id', $user->perumahan_id))
            ],
            'cost_centre_code' => 'required|string|exists:cost_centres,cost_centre_code',
            'description' => 'required|string'
        ]);

        $costCentre = CostCentre::where('cost_centre_code', $validated['cost_centre_code'])
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costCentre) {
            return response()->json(['error' => 'Unauthorized: Cost Centre tidak ditemukan atau bukan milik perumahan Anda.'], 403);
        }

        $validated['perumahan_id'] = $user->perumahan_id;

        $costElement = CostElement::create($validated);

        return response()->json([
            'message' => 'Cost Element berhasil ditambahkan',
            'data' => $costElement
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $costElement = CostElement::where('id', $id)
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costElement) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk mengupdate data ini.'], 403);
        }

        $validated = $request->validate([
            'cost_element_code' => [
                'required',
                'string',
                Rule::unique('cost_elements')->where(fn($q) => $q->where('perumahan_id', $user->perumahan_id))->ignore($id),
            ],
            'cost_centre_code' => 'required|string|exists:cost_centres,cost_centre_code',
            'description' => 'required|string'
        ]);

        $costCentre = CostCentre::where('cost_centre_code', $validated['cost_centre_code'])
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costCentre) {
            return response()->json(['error' => 'Unauthorized: Cost Centre tidak ditemukan atau bukan milik perumahan Anda.'], 403);
        }

        $costElement->update($validated);

        return response()->json([
            'message' => 'Cost Element berhasil diupdate',
            'data' => $costElement
        ], 200);
    }

    // Menghapus cost element
    public function destroy($id)
    {
        $user = auth()->user();
        $costElement = CostElement::where('id', $id)
            ->whereHas('costCentre', function ($query) use ($user) {
                $query->where('perumahan_id', $user->perumahan_id);
            })
            ->first();

        if (!$costElement) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk menghapus data ini.'], 403);
        }

        $costElement->delete();

        return response()->json([
            'message' => 'Cost Element berhasil dihapus'
        ], 204);
    }
}
