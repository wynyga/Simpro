<?php

namespace App\Http\Controllers;

use App\Models\BlokUnit;
use App\Models\TipeRumah;
use Illuminate\Http\Request;

class BlokUnitController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // Menampilkan daftar blok unit
    public function index()
    {
        $blok_units = BlokUnit::with('tipeRumah.perumahan')->get();  // Mengambil blok unit dengan tipe rumah dan perumahan
        if ($blok_units->isEmpty()) {
            return response()->json(['message' => 'Tidak ada blok unit ditemukan'], 404);
        }
        return response()->json($blok_units);
        //return view('blokunit.index', compact('blok_units'));
    }

    public function create()
    {
        $tipe_rumah = TipeRumah::all();  // Mengambil semua data tipe rumah
        if ($tipe_rumah->isEmpty()) {
            return response()->json(['message' => 'Tipe rumah tidak ditemukan'], 404);
        }
        return response()->json($tipe_rumah);
        //return view('blokunit.create', compact('tipe_rumah'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'blok' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'id_tipe_rumah' => 'required|exists:tipe_rumah,id',
        ]);
    
        // Jika tipe rumah ada, simpan data blok unit
        $blok_unit = BlokUnit::create($validated);
        return response()->json([
            'message' => 'Blok berhasil ditambahkan',
            'data' => $blok_unit
        ], 201);
    }
    
}
