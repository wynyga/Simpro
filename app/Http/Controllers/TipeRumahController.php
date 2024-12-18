<?php

namespace App\Http\Controllers;

use App\Models\TipeRumah;
use App\Models\Perumahan;
use Illuminate\Http\Request;

class TipeRumahController extends Controller
{
    public function index()
    {
        $tipe_rumahs = TipeRumah::with('perumahan')->get();
        return response()->json($tipe_rumahs);
        // return view('tipe_rumah.index',compact('tipe_rumahs'));
    }

    public function create()
    {
        $perumahans = Perumahan::all();  // Mengambil semua data perumahan
        return response()->json($perumahans); 
        //return view('tipe_rumah.create', compact('perumahans'));
    }

    public function store(Request $request)
    {
        if (!Perumahan::find($request->id_perumahan)) {
            return response()->json([
                'message' => 'Nama perumahan tidak valid'
            ], 404);
        }

        $validated = $request->validate([
            'id_perumahan' => 'required|exists:perumahan,id',
            'tipe_rumah' => 'required|string|max:255',
            'luas_bangunan' => 'required|numeric',
            'luas_kavling' => 'required|numeric',
            'harga_standar_tengah' => 'required|numeric',
            'harga_standar_sudut' => 'required|numeric',
            'penambahan_bangunan' => 'required|numeric',
        ]);

        $tipeRumah = TipeRumah::create($validated);
        return response()->json([
            'message' => 'Tipe rumah berhasil ditambahkan',
            'data' => $tipeRumah
        ], 201);
        //return redirect()->route('tipe_rumah.index')->with('success', 'Tipe rumah berhasil ditambahkan.');
    }
}
