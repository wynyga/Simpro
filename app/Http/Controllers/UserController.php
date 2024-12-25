<?php

namespace App\Http\Controllers;

use App\Models\UserPerumahan;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $users = UserPerumahan::all();  // Mengambil semua data user
        return response()->json($users);
        // return view('user.index', compact('users'));  // Mengirim data ke view
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'nama_user' => 'required|string|max:255',
            'alamat_user' => 'required|string|max:255',
            'no_telepon' => 'required|digits_between:10,15',
        ]);
    
        $user = UserPerumahan::create($data);
        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }
    
}
