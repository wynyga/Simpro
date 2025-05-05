<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GudangIn;
use App\Models\GudangOut;

class GudangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        $gudangIns = GudangIn::with('sttb') // tambahkan relasi
            ->where('perumahan_id', $perumahanId)
            ->get();
    
        $gudangOuts = GudangOut::where('perumahan_id', $perumahanId)->get();
    
        return response()->json([
            'gudang_in' => $gudangIns,
            'gudang_out' => $gudangOuts
        ]);
    }
    
}
