<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPerumahan;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar pengguna berdasarkan perumahan yang terautentikasi.
     */
    public function index()
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $users = UserPerumahan::where('perumahan_id', $perumahanId)->get();

        return response()->json(['users' => $users]);
    }

    /**
     * Menyimpan pengguna baru ke dalam sistem.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
       
        $validated = $request->validate([
            'nama_user' => 'required|string|max:255',
            'alamat_user' => 'required|string|max:255',
            'no_telepon' => 'required|digits_between:10,15',
        ]);

        $validated['perumahan_id'] = $user->perumahan_id;

        $userPerumahan = UserPerumahan::create($validated);

        return response()->json([
            'message' => 'User berhasil ditambahkan.',
            'data' => $userPerumahan
        ], 201);
    }

    /**
     * Memperbarui informasi pengguna berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $userPerumahan = UserPerumahan::where('id', $id)
            ->where('perumahan_id', $perumahanId)
            ->first();

        if (!$userPerumahan) {
            return response()->json(['error' => 'User tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama_user' => 'sometimes|required|string|max:255',
            'alamat_user' => 'sometimes|required|string|max:255',
            'no_telepon' => 'sometimes|required|digits_between:10,15',
        ]);

        $userPerumahan->update($validated);

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'data' => $userPerumahan
        ], 200);
    }

    /**
     * Menghapus pengguna berdasarkan ID.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $userPerumahan = UserPerumahan::where('id', $id)
            ->where('perumahan_id', $perumahanId)
            ->first();

        if (!$userPerumahan) {
            return response()->json(['error' => 'User tidak ditemukan.'], 404);
        }

        $userPerumahan->delete();

        return response()->json(['message' => 'User berhasil dihapus.'], 200);
    }
}
