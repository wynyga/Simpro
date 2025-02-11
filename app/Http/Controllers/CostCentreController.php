<?php

namespace App\Http\Controllers;

use App\Models\CostCentre;
use Illuminate\Http\Request;

class CostCentreController extends Controller
{
    public function index()
    {
        return response()->json(CostCentre::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_centre_code' => 'required|string|unique:cost_centres,cost_centre_code',
            'description' => 'required|string'
        ]);

        $costCentre = CostCentre::create($validated);

        return response()->json([
            'message' => 'Cost Centre berhasil ditambahkan',
            'data' => $costCentre
        ], 201);
    }
}
