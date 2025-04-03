<?php

namespace App\Http\Controllers;

use App\Models\CostTee;
use App\Models\CostElement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CostTeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $costTees = CostTee::where('perumahan_id', $user->perumahan_id)
            ->with('costElement')
            ->get();
    
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

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'cost_tee_code' => [
                'required',
                'string',
                Rule::unique('cost_tees')->where(fn($q) => $q->where('perumahan_id', $user->perumahan_id))
            ],
            'cost_element_code' => 'required|string|exists:cost_elements,cost_element_code',
            'description' => 'required|string'
        ]);

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

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $costTee = CostTee::where('id', $id)
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costTee) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk mengupdate data ini.'], 403);
        }

        $validated = $request->validate([
            'cost_tee_code' => [
                'required',
                'string',
                Rule::unique('cost_tees')->where(fn($q) => $q->where('perumahan_id', $user->perumahan_id))->ignore($id),
            ],
            'cost_element_code' => 'required|string|exists:cost_elements,cost_element_code',
            'description' => 'required|string'
        ]);

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
