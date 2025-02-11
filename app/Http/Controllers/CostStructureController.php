<?php

namespace App\Http\Controllers;

use App\Models\CostStructure;
use Illuminate\Http\Request;

class CostStructureController extends Controller
{
    public function index()
    {
        return response()->json(CostStructure::with('costTee')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_tee_code' => 'required|string|exists:cost_tees,cost_tee_code',
            'cost_code' => 'required|string|in:KASIN,KASOUT',
            'description' => 'required|string'
        ]);

        $costStructure = CostStructure::create($validated);

        return response()->json([
            'message' => 'Cost Structure berhasil ditambahkan',
            'data' => $costStructure
        ], 201);
    }
}


