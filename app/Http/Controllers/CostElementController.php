<?php

namespace App\Http\Controllers;

use App\Models\CostElement;
use Illuminate\Http\Request;

class CostElementController extends Controller
{
    public function index()
    {
        return response()->json(CostElement::with('costCentre')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_element_code' => 'required|string|unique:cost_elements,cost_element_code',
            'cost_centre_code' => 'required|string|exists:cost_centres,cost_centre_code',
            'description' => 'required|string'
        ]);

        $costElement = CostElement::create($validated);

        return response()->json([
            'message' => 'Cost Element berhasil ditambahkan',
            'data' => $costElement
        ], 201);
    }
}
