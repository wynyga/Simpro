<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use Illuminate\Http\Request;

class BlokController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // Menampilkan semua blok berdasarkan perumahan pengguna
    public function index()
    {
        $user = auth()->user();
        $bloks = Blok::where('perumahan_id', $user->perumahan_id)->get();
        
        if ($bloks->isEmpty()) {
            return response()->json(['message' => 'Tidak ada blok ditemukan'], 404);
        }

        return response()->json($bloks);
    }

    // Menampilkan detail blok tertentu
    public function show($id)
    {
        $user = auth()->user();
        $blok = Blok::where('id', $id)->where('perumahan_id', $user->perumahan_id)->first();

        if (!$blok) {
            return response()->json(['error' => 'Blok tidak ditemukan atau tidak ada akses'], 404);
        }

        return response()->json($blok);
    }

    // Menyimpan blok baru
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama_blok' => 'required|string|max:255'
        ]);

        $validated['perumahan_id'] = $user->perumahan_id;

        $blok = Blok::create($validated);

        return response()->json([
            'message' => 'Blok berhasil ditambahkan',
            'data' => $blok
        ], 201);
    }

    // Memperbarui blok
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $blok = Blok::where('id', $id)->where('perumahan_id', $user->perumahan_id)->first();

        if (!$blok) {
            return response()->json(['error' => 'Unauthorized: You cannot update data in another perumahan.'], 403);
        }

        $validated = $request->validate([
            'nama_blok' => 'required|string|max:255'
        ]);

        $blok->update($validated);

        return response()->json([
            'message' => 'Blok berhasil diupdate',
            'data' => $blok
        ], 200);
    }

    // Menghapus blok
    public function destroy($id)
    {
        $user = auth()->user();
        $blok = Blok::where('id', $id)->where('perumahan_id', $user->perumahan_id)->first();

        if (!$blok) {
            return response()->json(['error' => 'Unauthorized: You cannot delete data in another perumahan.'], 403);
        }

        $blok->delete();

        return response()->json([
            'message' => 'Blok berhasil dihapus'
        ], 204);
    }
}
