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
    
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user->perumahan_id) {
            return response()->json(['error' => 'Access Denied: No perumahan assigned to user.'], 403);
        }
    
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
    
        $query = TipeRumah::where('perumahan_id', $user->perumahan_id);
    
        if ($search) {
            $query->where('tipe_rumah', 'like', "%{$search}%");
        }
    
        $tipeRumah = $query->paginate($perPage);
    
        return response()->json($tipeRumah);
    }
    
    public function all()
    {
        $user = auth()->user();

        if (!$user->perumahan_id) {
            return response()->json(['error' => 'User tidak memiliki perumahan_id.'], 403);
        }

        $tipeRumah = \App\Models\TipeRumah::where('perumahan_id', $user->perumahan_id)->get();

        return response()->json($tipeRumah);
    }    

    public function create()
    {
        $user = auth()->user();
        if (!$user->perumahan_id) {
            return response()->json(['error' => 'Access Denied: No perumahan assigned to user.'], 403);
        }
    
        $perumahans = Perumahan::where('id', $user->perumahan_id)->get();
        return response()->json($perumahans);
    }
    

    public function store(Request $request)
    {
        $user = auth()->user();
    
        // Validasi input tanpa perumahan_id dalam body request
        $validated = $request->validate([
            'tipe_rumah' => 'required|string|max:255',
            'luas_bangunan' => 'required|numeric',
            'luas_kavling' => 'required|numeric',
            'harga_standar_tengah' => 'required|numeric',
            'harga_standar_sudut' => 'required|numeric',
            'penambahan_bangunan' => 'required|numeric',
        ]);
    
        // Tambahkan perumahan_id dari pengguna terotentikasi
        $validated['perumahan_id'] = $user->perumahan_id;
    
        // Cek apakah kombinasi tipe rumah dan perumahan_id sudah ada
        $exists = TipeRumah::where('tipe_rumah', $request->tipe_rumah)
                           ->where('perumahan_id', $user->perumahan_id)
                           ->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Nama tipe rumah telah ada'
            ], 409);
        }
    
        // Jika semua validasi lolos, buat tipe rumah baru
        $tipeRumah = TipeRumah::create($validated);
        return response()->json([
            'message' => 'Tipe rumah berhasil ditambahkan',
            'data' => $tipeRumah
        ], 201);
    }
    

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $tipeRumah = TipeRumah::findOrFail($id);
    
        if ($tipeRumah->perumahan_id != $user->perumahan_id) {
            return response()->json(['error' => 'Unauthorized: You cannot update data in another perumahan.'], 403);
        }
    
        $validated = $request->validate([
            'tipe_rumah' => 'required|string|max:255',
            'luas_bangunan' => 'required|numeric',
            'luas_kavling' => 'required|numeric',
            'harga_standar_tengah' => 'required|numeric',
            'harga_standar_sudut' => 'required|numeric',
            'penambahan_bangunan' => 'required|numeric',
        ]);
    
        // Update data yang sudah ada
        $tipeRumah->update($validated);
    
        return response()->json([
            'message' => 'Tipe rumah berhasil diupdate',
            'data' => $tipeRumah
        ], 200);
    }
    

    public function destroy($id)
    {
        $user = auth()->user();
        $tipeRumah = TipeRumah::findOrFail($id);
        if ($tipeRumah->perumahan_id != $user->perumahan_id) {
            return response()->json(['error' => 'Unauthorized: You cannot delete data in another perumahan.'], 403);
        }
        $tipeRumah->delete();

        return response()->json([
            'message' => 'Tipe rumah berhasil dihapus'
        ], 204);
    } 

    public function show($id)
    {
        $user = auth()->user();
        
        // Cari tipe rumah berdasarkan ID
        $tipeRumah = TipeRumah::where('id', $id)
                            ->where('perumahan_id', $user->perumahan_id)
                            ->first();

        if (!$tipeRumah) {
            return response()->json(['error' => 'Tipe rumah tidak ditemukan atau akses ditolak.'], 404);
        }

        return response()->json($tipeRumah);
    }

}
