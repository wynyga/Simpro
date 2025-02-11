<?php

namespace App\Http\Controllers;

use App\Models\LapBulanan;
use Illuminate\Http\Request;

class LapBulananController extends Controller
{
    public function index()
    {
        $laporans = LapBulanan::with('costStructure.costTee')->get();
    
        $laporans = $laporans->map(function ($laporan) {
            return [
                'id' => $laporan->id,
                'cost_structure_id' => $laporan->cost_structure_id,
                'bulan' => $laporan->bulan,
                'tahun' => $laporan->tahun,
                'jumlah' => $laporan->jumlah,
                'created_at' => $laporan->created_at,
                'updated_at' => $laporan->updated_at,
                'code_account' => $laporan->code_account, // Tambahkan Code Account
                'cost_structure' => [
                    'id' => $laporan->costStructure->id,
                    'cost_tee_code' => $laporan->costStructure->cost_tee_code,
                    'cost_code' => $laporan->costStructure->cost_code,
                    'description' => $laporan->costStructure->description,
                    'created_at' => $laporan->costStructure->created_at,
                    'updated_at' => $laporan->costStructure->updated_at,
                    'cost_tee' => [
                        'id' => $laporan->costStructure->costTee->id,
                        'cost_tee_code' => $laporan->costStructure->costTee->cost_tee_code,
                        'cost_element_code' => $laporan->costStructure->costTee->cost_element_code,
                        'description' => $laporan->costStructure->costTee->description,
                        'created_at' => $laporan->costStructure->costTee->created_at,
                        'updated_at' => $laporan->costStructure->costTee->updated_at
                    ]
                ]
            ];
        });
    
        return response()->json($laporans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_structure_id' => 'required|exists:cost_structures,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric'
        ]);
    
        $laporan = LapBulanan::create($validated);
    
        return response()->json([
            'message' => 'Laporan bulanan berhasil disimpan',
            'data' => $laporan
        ], 201);
    }
    
}


