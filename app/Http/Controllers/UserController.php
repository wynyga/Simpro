<?php

namespace App\Http\Controllers;

use App\Models\UserPerumahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $perumahanId = Auth::user()->perumahan_id;
        $users = UserPerumahan::where('perumahan_id', $perumahanId)->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $perumahanId = Auth::user()->perumahan_id;
        $data = $request->validate([
            'nama_user' => 'required|string|max:255',
            'alamat_user' => 'required|string|max:255',
            'no_telepon' => 'required|digits_between:10,15',
        ]);
    
        $data['perumahan_id'] = $perumahanId;
        $user = UserPerumahan::create($data);
        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $perumahanId = Auth::user()->perumahan_id;
        $user = UserPerumahan::where('id', $id)->where('perumahan_id', $perumahanId)->firstOrFail();

        $data = $request->validate([
            'nama_user' => 'sometimes|required|string|max:255',
            'alamat_user' => 'sometimes|required|string|max:255',
            'no_telepon' => 'sometimes|required|digits_between:10,15',
        ]);

        $user->update($data);
        return response()->json([
            'message' => 'User berhasil diperbarui',
            'data' => $user
        ], 200);
    }

    public function destroy($id)
    {
        $perumahanId = Auth::user()->perumahan_id;
        $user = UserPerumahan::where('id', $id)->where('perumahan_id', $perumahanId)->firstOrFail();

        $user->delete();
        return response()->json([
            'message' => 'User berhasil dihapus'
        ], 204);
    }
}
