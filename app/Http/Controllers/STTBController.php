<?php

namespace App\Http\Controllers;

use App\Models\Sttb;
use App\Models\GudangIn;
use App\Models\Perumahan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SttbController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'gudang_in_id' => 'required|exists:gudang_in,id',
            'jenis_penerimaan' => 'required|in:Langsung,Tidak Langsung,Ambil Sendiri',
        ]);
    
        $gudangIn = GudangIn::findOrFail($request->gudang_in_id);
    
        if ($gudangIn->status !== 'verified') {
            return response()->json(['message' => 'Barang belum diverifikasi.'], 422);
        }
    
        if (Sttb::where('gudang_in_id', $gudangIn->id)->exists()) {
            return response()->json(['message' => 'STTB sudah pernah dibuat untuk barang ini.'], 409);
        }
    
        $perumahan = Perumahan::findOrFail($gudangIn->perumahan_id);
        $latestId = Sttb::max('id') + 1;
        $no_doc = sprintf("%02d/TB-%s/%d", $latestId, $perumahan->inisial, now()->year);
    
        $sttb = Sttb::create([
            'gudang_in_id' => $gudangIn->id,
            'perumahan_id' => $perumahan->id,
            'no_doc' => $no_doc,
            'tanggal' => now(),
            'nama_barang' => $gudangIn->nama_barang,
            'nama_supplier' => $gudangIn->pengirim,
            'jumlah' => $gudangIn->jumlah,
            'satuan' => $gudangIn->satuan,
            'pengirim' => $gudangIn->pengirim,
            'jenis_penerimaan' => $request->jenis_penerimaan, // <-- Ambil dari request, bukan dari gudangIn
            'diserahkan_oleh' => $gudangIn->pengirim,
            'diterima_oleh' => auth()->user()->name,
            'mengetahui' => null,
        ]);
    
        return response()->json($sttb);
    }
    

    public function show($id)
    {
        $sttb = Sttb::with(['gudangIn', 'perumahan'])->findOrFail($id);
        return response()->json($sttb);
    }

    public function cetak($id)
    {
        $sttb = Sttb::with(['gudangIn', 'perumahan'])->findOrFail($id);

        $pdf = Pdf::loadView('sttb.template', compact('sttb'))->setPaper('A5', 'portrait');

        $safeNoDoc = str_replace(['/', '\\'], '-', $sttb->no_doc);

        return $pdf->download("sttb-{$safeNoDoc}.pdf");
    }
}
