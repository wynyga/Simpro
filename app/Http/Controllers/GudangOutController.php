<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GudangOut;
use App\Helpers\StockHelper; 

class GudangOutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan semua transaksi Gudang Out berdasarkan perumahan_id pengguna
    public function index()
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $gudangOuts = GudangOut::where('perumahan_id', $perumahanId)->get();
        return response()->json($gudangOuts);
    }

    // Operator Menambahkan Data Gudang Out (Status Awal: Pending)
    public function store(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $validated = $request->validate([
            'kode_barang' => 'required',
            'tanggal' => 'required|date',
            'peruntukan' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable'
        ]);

        $stockModel = StockHelper::getModelFromCode($request->kode_barang, $perumahanId);
        if (!$stockModel) {
            return response()->json(['message' => 'Barang tidak ditemukan di stok.'], 404);
        }

        if ($stockModel->stock_bahan < $request->jumlah) {
            return response()->json(['message' => 'Stok tidak cukup.'], 400);
        }

        $jumlahHarga = $request->jumlah * $stockModel->harga_satuan;

        $gudangOut = new GudangOut();
        $gudangOut->perumahan_id = $perumahanId;
        $gudangOut->kode_barang = $request->kode_barang;
        $gudangOut->nama_barang = $stockModel->nama_barang;
        $gudangOut->tanggal = $request->tanggal;
        $gudangOut->peruntukan = $request->peruntukan;
        $gudangOut->jumlah = $request->jumlah;
        $gudangOut->satuan = $stockModel->satuan;
        $gudangOut->jumlah_harga = $jumlahHarga;
        $gudangOut->keterangan = $request->keterangan;
        $gudangOut->status = 'pending'; // Status awal pending
        $gudangOut->save();

        return response()->json([
            'message' => 'Data Gudang Out berhasil disimpan dengan status pending. Menunggu verifikasi.',
            'data' => $gudangOut
        ], 201);
    }

    // Project Manager Verifikasi Gudang Out
    public function verify(Request $request, $id)
    {
        $gudangOut = GudangOut::findOrFail($id);

        if ($gudangOut->status !== 'pending') {
            return response()->json(['message' => 'Transaksi ini sudah diproses sebelumnya.'], 400);
        }

        $stockModel = StockHelper::getModelFromCode($gudangOut->kode_barang, $gudangOut->perumahan_id);

        if (!$stockModel || $stockModel->stock_bahan < $gudangOut->jumlah) {
            return response()->json(['message' => 'Stok tidak cukup atau barang tidak ditemukan saat verifikasi.'], 400);
        }

        $stockModel->stock_bahan -= $gudangOut->jumlah;
        $stockModel->save();

        $gudangOut->status = 'verified';
        $gudangOut->save();

        return response()->json([
            'message' => 'Transaksi Gudang Out telah diverifikasi dan stok diperbarui.',
            'data' => $gudangOut
        ]);
    }

    // Project Manager Menolak Transaksi Gudang Out
    public function reject(Request $request, $id)
    {
        $gudangOut = GudangOut::findOrFail($id);

        if ($gudangOut->status !== 'pending') {
            return response()->json(['message' => 'Transaksi ini sudah diproses sebelumnya.'], 400);
        }

        $gudangOut->status = 'rejected';
        $gudangOut->save();

        return response()->json([
            'message' => 'Transaksi Gudang Out ditolak.',
            'data' => $gudangOut
        ]);
    }
}
