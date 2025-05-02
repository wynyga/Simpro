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
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
    
        $query = Blok::where('perumahan_id', $user->perumahan_id);
    
        if ($search) {
            $query->where('nama_blok', 'like', "%{$search}%");
        }
    
        $bloks = $query->paginate($perPage);
    
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

    public function all()
    {
        $user = auth()->user();

        $bloks = \App\Models\Blok::where('perumahan_id', $user->perumahan_id)->get();

        return response()->json($bloks);
    }

}
