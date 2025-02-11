<?php

namespace App\Http\Controllers;

use App\Models\CostStructure;
use Illuminate\Http\Request;

class CostStructureController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_tree' => 'required|string|unique:cost_structures,cost_tree',
            'cost_element' => 'required|string',
            'cost_centre' => 'required|string',
            'cost_code' => 'required|string',
            'description' => 'required|string'
        ]);

        $costStructure = CostStructure::create($validated);

        return response()->json([
            'message' => 'Cost Structure berhasil ditambahkan',
            'data' => $costStructure
        ], 201);
    }
}

