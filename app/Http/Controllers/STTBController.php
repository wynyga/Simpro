<?php

namespace App\Http\Controllers;

use App\Models\Sttb;
use App\Models\GudangIn;
use App\Models\Perumahan;
use App\Models\Kwitansi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\KwitansiService;

class SttbController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'gudang_in_id' => 'required|exists:gudang_in,id',
        ]);
    
        $gudangIn = GudangIn::findOrFail($request->gudang_in_id);
    
        if ($gudangIn->status !== 'verified') {
            return response()->json(['message' => 'Barang belum diverifikasi.'], 422);
        }
    
        if (Sttb::where('gudang_in_id', $gudangIn->id)->exists()) {
            return response()->json(['message' => 'STTB sudah pernah dibuat untuk barang ini.'], 409);
        }
    
        $perumahan = Perumahan::findOrFail($gudangIn->perumahan_id);
        $lastSttb = Sttb::where('perumahan_id', $perumahan->id)
        ->orderByDesc('id')
        ->first();
    
        $nextSttbNumber = 1;
        
        if ($lastSttb && preg_match('/^(\d{2})\/TB-/', $lastSttb->no_doc, $match)) {
            $nextSttbNumber = (int)$match[1] + 1;
        }
        
        $no_doc_sttb = sprintf("%02d/TB-%s/%d", $nextSttbNumber, $perumahan->inisial, now()->year);
    
    
        $sttb = Sttb::create([
            'gudang_in_id' => $gudangIn->id,
            'perumahan_id' => $perumahan->id,
            'no_doc' => $no_doc_sttb,
            'tanggal' => now(),
            'nama_barang' => $gudangIn->nama_barang,
            'jumlah' => $gudangIn->jumlah,
            'satuan' => $gudangIn->satuan,
            'nama_supplier' => $gudangIn->pengirim,
            'diserahkan_oleh' => $gudangIn->pengirim,
            'diterima_oleh' => auth()->user()->name,
            'mengetahui' => null,
        ]);
    
        $kwitansiCO = null;
        $validJenis = ['Cash', 'Transfer Bank', 'Giro', 'Cek', 'Draft'];
    
        if (in_array($gudangIn->sistem_pembayaran, $validJenis)) {
            $no_doc_co = KwitansiService::generateNoDoc($perumahan->id, 'CO');
    
            $kwitansiCO = Kwitansi::create([
                'gudang_in_id' => $gudangIn->id, 
                'perumahan_id' => $perumahan->id,
                'no_doc' => $no_doc_co,
                'tanggal' => now(),
                'dari' => $gudangIn->pengirim,
                'jumlah' => $gudangIn->jumlah_harga,
                'untuk_pembayaran' => 'Pembayaran ' . $gudangIn->sistem_pembayaran . ' atas Barang ' . $gudangIn->nama_barang,
                'metode_pembayaran' => $gudangIn->sistem_pembayaran,
                'dibuat_oleh' => auth()->user()->name,
                'disetor_oleh' => $gudangIn->pengirim,
                'mengetahui' => null,
            ]);
        }
    
        return response()->json([
            'sttb' => $sttb,
            'kwitansi_co' => $kwitansiCO,
        ]);
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
