<?php

namespace App\Http\Controllers;

use App\Models\TipeRumah;
use App\Models\Perumahan;
use Illuminate\Http\Request;

class TipeRumahController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $tipe_rumahs = TipeRumah::with('perumahan')->get();
        return response()->json($tipe_rumahs);
    }

    public function create()
    {
        $perumahans = Perumahan::all();
        return response()->json($perumahans);
    }

    public function store(Request $request)
    {
        // Validasi input dengan pesan error khusus
        $validated = $request->validate([
            'id_perumahan' => 'required|exists:perumahan,id',
            'tipe_rumah' => 'required|string|max:255',
            'luas_bangunan' => 'required|numeric',
            'luas_kavling' => 'required|numeric',
            'harga_standar_tengah' => 'required|numeric',
            'harga_standar_sudut' => 'required|numeric',
            'penambahan_bangunan' => 'required|numeric',
        ], [
            'id_perumahan.exists' => 'Nama Perumahan tidak ada'  // Pesan khusus untuk validasi id_perumahan
        ]);
    
        // Cek apakah kombinasi tipe rumah dan id perumahan sudah ada
        $exists = TipeRumah::where('tipe_rumah', $request->tipe_rumah)
                           ->where('id_perumahan', $request->id_perumahan)
                           ->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Nama tipe perumahan telah ada'
            ], 409);
        }
    
        // Jika semua validasi lolos, buat tipe rumah baru
        $tipeRumah = TipeRumah::create($validated);
        return response()->json([
            'message' => 'Tipe rumah berhasil ditambahkan',
            'data' => $tipeRumah
        ], 201);
    }
    
    
}
