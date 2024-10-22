<?php

namespace App\Http\Controllers;

use App\Models\UserPerumahan;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = UserPerumahan::all();  // Mengambil semua data user
        return view('user.index', compact('users'));  // Mengirim data ke view
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_user' => 'required',
            'alamat_user' => 'required',
            'no_telepon' => 'required',
        ]);

        UserPerumahan::create($data);  // Menyimpan data user ke database
        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }
}
