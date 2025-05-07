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
    
        $gudangIns = GudangIn::with('sttb', 'kwitansiCo')
        ->where('perumahan_id', $perumahanId)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_barang' => $item->nama_barang,
                'pengirim' => $item->pengirim,
                'no_nota' => $item->no_nota,
                'tanggal_barang_masuk' => $item->tanggal_barang_masuk,
                'jumlah' => $item->jumlah,
                'status' => $item->status,
                'sttb' => $item->sttb ? [
                    'id' => $item->sttb->id,
                    'no_doc' => $item->sttb->no_doc,
                    'tanggal' => $item->sttb->tanggal,
                ] : null,
                'kwitansi_co' => $item->kwitansiCo ? [
                    'id' => $item->kwitansiCo->id,
                    'no_doc' => $item->kwitansiCo->no_doc,
                    'tanggal' => $item->kwitansiCo->tanggal,
                ] : null,
            ];
        });
    
    
        $gudangOuts = GudangOut::where('perumahan_id', $perumahanId)->get();
    
        return response()->json([
            'gudang_in' => $gudangIns,
            'gudang_out' => $gudangOuts
        ]);
    }
    
}
