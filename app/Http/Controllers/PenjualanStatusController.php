<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiKas;

class PenjualanStatusController extends Controller
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
    
        $filterStatus = $request->query('status'); // "lunas", "cicil", "utang"
    
        $transaksiList = Transaksi::where('perumahan_id', $perumahanId)
            ->with(['unit', 'userPerumahan'])
            ->get()
            ->map(function ($transaksi) {
                $totalBayar = TransaksiKas::where('sumber_transaksi', 'penjualan')
                    ->where('keterangan_transaksi_id', $transaksi->id)
                    ->where('status', 'approved')
                    ->sum('jumlah');
    
                if ($totalBayar >= $transaksi->plafon_kpr) {
                    $statusBayar = 'lunas';
                } elseif ($totalBayar > 0) {
                    $statusBayar = 'cicil';
                } else {
                    $statusBayar = 'utang';
                }
    
                return [
                    'id' => $transaksi->id,
                    'unit_id' => $transaksi->unit_id,
                    'user_id' => $transaksi->user_id,
                    'total_harga_jual' => $transaksi->total_harga_jual,
                    'plafon_kpr' => $transaksi->plafon_kpr,
                    'total_bayar' => (float) $totalBayar,
                    'status_bayar' => $statusBayar,
                    'unit' => $transaksi->unit ? $transaksi->unit->nomor_unit : null,
                    'pembeli' => $transaksi->userPerumahan ? $transaksi->userPerumahan->nama_user : null,
                ];
            });
    
        // Jika ada filter status
        if ($filterStatus) {
            $transaksiList = $transaksiList->where('status_bayar', $filterStatus)->values();
        }
    
        return response()->json($transaksiList);
    }
    
}
