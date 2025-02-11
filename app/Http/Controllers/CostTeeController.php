<?php

namespace App\Http\Controllers;

use App\Models\CostTee;
use Illuminate\Http\Request;

class CostTeeController extends Controller
{
    public function index()
    {
        return response()->json(CostTee::with('costElement')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_tee_code' => 'required|string|unique:cost_tees,cost_tee_code',
            'cost_element_code' => 'required|string|exists:cost_elements,cost_element_code',
            'description' => 'required|string'
        ]);

        $costTee = CostTee::create($validated);

        return response()->json([
            'message' => 'Cost Tee berhasil ditambahkan',
            'data' => $costTee
        ], 201);
    }
}
