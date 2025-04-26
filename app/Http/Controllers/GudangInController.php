<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GudangIn;
use App\Helpers\StockHelper;

class GudangInController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan semua transaksi Gudang In berdasarkan perumahan_id pengguna
    public function index()
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        $gudangIns = GudangIn::where('perumahan_id', $perumahanId)->get();
        return response()->json($gudangIns);
    }
     
    // Operator Menambahkan Data Gudang In (Status Awal: Pending)
    public function store(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        // Validasi input
        $validated = $request->validate([
            'kode_barang' => 'required',
            'pengirim' => 'required',
            'no_nota' => 'required',
            'tanggal_barang_masuk' => 'required|date',
            'sistem_pembayaran' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable'
        ]);
        
        // ambil stockModel berdasarkan $validated['kode_barang']
        $stockModel = StockHelper::getModelFromCode($validated['kode_barang'], $perumahanId);
        if (!$stockModel) {
            return response()->json([
                'message' => 'Barang tidak ditemukan di stok.',
            ], 404);
        }
        
        // hitung jumlah harga otomatis
        $jumlahHarga = $validated['jumlah'] * $stockModel->harga_satuan;
        
        // simpan
        $gudangIn = new GudangIn();
        $gudangIn->perumahan_id = $perumahanId;
        $gudangIn->kode_barang = $validated['kode_barang'];
        $gudangIn->nama_barang = $stockModel->nama_barang;
        $gudangIn->pengirim = $validated['pengirim'];
        $gudangIn->no_nota = $validated['no_nota'];
        $gudangIn->tanggal_barang_masuk = $validated['tanggal_barang_masuk'];
        $gudangIn->sistem_pembayaran = $validated['sistem_pembayaran'];
        $gudangIn->jumlah = $validated['jumlah'];
        $gudangIn->satuan = $stockModel->satuan;
        $gudangIn->harga_satuan = $stockModel->harga_satuan;
        $gudangIn->jumlah_harga = $jumlahHarga;
        $gudangIn->keterangan = $validated['keterangan'] ?? null;
        $gudangIn->status = 'pending';
        $gudangIn->save();
        

        return response()->json([
            'message' => 'Data Gudang In berhasil disimpan dengan status pending. Menunggu verifikasi.',
            'data' => $gudangIn
        ], 201);
    }                                            

    // Project Manager Verifikasi Gudang In
    public function verify( $id)
    {
        $gudangIn = GudangIn::findOrFail($id);

        if ($gudangIn->status !== 'pending') {
            return response()->json(['message' => 'Transaksi ini sudah diverifikasi sebelumnya.'], 400);
        }

        $gudangIn->status = 'verified';
        $gudangIn->save();

        // Jika status diverifikasi, baru update stok
        $stockModel = StockHelper::getModelFromCode($gudangIn->kode_barang, $gudangIn->perumahan_id);
        if ($stockModel) {
            $stockModel->stock_bahan += $gudangIn->jumlah;
            $stockModel->save();
        }

        return response()->json([
            'message' => 'Transaksi Gudang In telah diverifikasi dan stok diperbarui.',
            'data' => $gudangIn
        ]);
    }

    // Project Manager Menolak Transaksi Gudang In
    public function reject( $id)
    {
        $gudangIn = GudangIn::findOrFail($id);

        if ($gudangIn->status !== 'pending') {
            return response()->json(['message' => 'Transaksi ini sudah diproses sebelumnya.'], 400);
        }

        $gudangIn->status = 'rejected';
        $gudangIn->save();

        return response()->json([
            'message' => 'Transaksi Gudang In ditolak.',
            'data' => $gudangIn
        ]);
    }
}
