<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKas;

class TransaksiKasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Menampilkan indeks data transaksi kas dengan output JSON.
     */
    public function index()
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        // Hanya hitung transaksi yang statusnya "approved"
        $totalCashIn = TransaksiKas::where('kode', '101')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved') // Tambahkan filter hanya untuk transaksi yang disetujui
            ->sum('jumlah');
    
        $totalCashOut = TransaksiKas::where('kode', '102')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved') // Tambahkan filter hanya untuk transaksi yang disetujui
            ->sum('jumlah');
    
        // Hitung saldo kas dengan hanya memperhitungkan transaksi yang telah disetujui
        $saldoKas = $totalCashIn - $totalCashOut;
    
        // Ambil semua transaksi, termasuk pending dan rejected
        $transaksiKas = TransaksiKas::where('perumahan_id', $perumahanId)->get()->map(function ($transaksi) {
            return [
                'id' => $transaksi->id,
                'tanggal' => $transaksi->tanggal,
                'keterangan_transaksi' => $transaksi->keterangan_transaksi,
                'kode' => $transaksi->kode,
                'jumlah' => $transaksi->jumlah ?? 0,
                'keterangan_objek_transaksi' => $transaksi->keterangan_objek_transaksi ?? "-",
                'metode_pembayaran' => $transaksi->metode_pembayaran ?? "Tunai",
                'saldo_setelah_transaksi' => $transaksi->saldo_setelah_transaksi ?? 0,
                'dibuat_oleh' => $transaksi->dibuat_oleh ?? "Admin",
                'status' => $transaksi->status, // Tambahkan status transaksi
            ];
        });
    
        return response()->json([
            'totalCashIn' => $totalCashIn,
            'totalCashOut' => $totalCashOut,
            'saldoKas' => $saldoKas,
            'transaksiKas' => $transaksiKas
        ]);
    }
    
    
    public function getJournalSummary($bulan, $tahun)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        // 1️⃣ Ambil total transaksi KASIN dan KASOUT hingga bulan tertentu
        $totalDebit = TransaksiKas::where('kode', '101') // KASIN
            ->where('perumahan_id', $perumahanId)
            ->whereYear('tanggal', '<=', $tahun)
            ->whereMonth('tanggal', '<=', $bulan)
            ->sum('jumlah');
    
        $totalKredit = TransaksiKas::where('kode', '102') // KASOUT
            ->where('perumahan_id', $perumahanId)
            ->whereYear('tanggal', '<=', $tahun)
            ->whereMonth('tanggal', '<=', $bulan)
            ->sum('jumlah');
    
        // 2️⃣ Hitung saldo (seharusnya sama dengan saldo kas terakhir)
        $saldo = $totalDebit - $totalKredit;
    
        // 3️⃣ Format Response
    return response()->json([
        'sheet_balance' => [
            'total_rp' => round($saldo, 2), // Menyimpan sebagai float dengan 2 desimal
            'color' => $saldo >= 0 ? 'green' : 'red'
        ],
        'debit' => round($totalDebit, 2), // Tetap float
        'kredit' => round($totalKredit, 2), // Tetap float
        'saldo' => round($saldo, 2) // Tetap float
    ]);
    }
    

    public function store(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        // Validasi input
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'keterangan_transaksi' => 'required|string',
            'kode' => 'required|in:101,102', // Hanya menerima kode 101 atau 102
            'jumlah' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:Tunai,Transfer Bank', // Tambahkan validasi metode pembayaran
            'keterangan_objek_transaksi' => 'nullable|string'
        ]);
    
        // Simpan transaksi baru dengan status awal "pending"
        $transaksi = TransaksiKas::create([
            'tanggal' => $validated['tanggal'],
            'keterangan_transaksi' => $validated['keterangan_transaksi'],
            'kode' => $validated['kode'],
            'jumlah' => $validated['jumlah'],
            'saldo_setelah_transaksi' => null, // Saldo akan dihitung saat transaksi disetujui
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'dibuat_oleh' => $user->name, // Ambil nama user yang sedang login
            'keterangan_objek_transaksi' => $validated['keterangan_objek_transaksi'],
            'perumahan_id' => $perumahanId,
            'status' => 'pending' // Status awal pending sebelum diverifikasi
        ]);
    
        // Mengembalikan respons JSON
        return response()->json([
            'message' => 'Transaksi KAS berhasil disimpan dan menunggu persetujuan.',
            'data' => $transaksi
        ], 201);
    }
    

    public function approveTransaction($id)
    {
        $transaksi = TransaksiKas::find($id);
        if (!$transaksi) {
            return response()->json(['error' => 'Transaksi tidak ditemukan.'], 404);
        }
    
        if ($transaksi->status !== 'pending') {
            return response()->json(['error' => 'Transaksi sudah diproses sebelumnya.'], 400);
        }
    
        // Hitung ulang saldo setelah transaksi ini disetujui
        $totalCashIn = TransaksiKas::where('kode', '101')->where('status', 'approved')->sum('jumlah');
        $totalCashOut = TransaksiKas::where('kode', '102')->where('status', 'approved')->sum('jumlah');
        $saldoSebelumnya = $totalCashIn - $totalCashOut;
    
        $saldoSetelahTransaksi = ($transaksi->kode === '101')
            ? $saldoSebelumnya + $transaksi->jumlah
            : $saldoSebelumnya - $transaksi->jumlah;
    
        // Update status menjadi "approved" dan set saldo setelah transaksi
        $transaksi->update([
            'status' => 'approved',
            'saldo_setelah_transaksi' => $saldoSetelahTransaksi
        ]);
    
        return response()->json(['message' => 'Transaksi berhasil disetujui.', 'data' => $transaksi]);
    }
    
    public function rejectTransaction($id)
    {
        $transaksi = TransaksiKas::find($id);
        if (!$transaksi) {
            return response()->json(['error' => 'Transaksi tidak ditemukan.'], 404);
        }
    
        if ($transaksi->status !== 'pending') {
            return response()->json(['error' => 'Transaksi sudah diproses sebelumnya.'], 400);
        }
    
        // Update status menjadi "rejected"
        $transaksi->update(['status' => 'rejected']);
    
        return response()->json(['message' => 'Transaksi berhasil ditolak.', 'data' => $transaksi]);
    }

    public function getHistory(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        // Ambil parameter filter dari request
        $status = $request->query('status'); // optional (approved, pending, rejected)
        $startDate = $request->query('start_date'); // optional (format: YYYY-MM-DD)
        $endDate = $request->query('end_date'); // optional (format: YYYY-MM-DD)
        $perPage = $request->query('per_page', 10); // default: 10 item per page

        // Query transaksi berdasarkan perumahan_id
        $query = TransaksiKas::where('perumahan_id', $perumahanId);

        // Filter berdasarkan status jika diberikan
        if ($status) {
            $query->where('status', $status);
        }

        // Filter berdasarkan rentang tanggal jika diberikan
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Ambil data dengan pagination
        $transaksiKas = $query->orderBy('tanggal', 'desc')->paginate($perPage);

        return response()->json($transaksiKas);
    }

    public function getRingkasanKasPerTahun(Request $request, $tahun = null)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
        $tahun = $tahun ?? date('Y'); // Default ke tahun ini jika tidak dipilih

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        // Hanya hitung transaksi yang statusnya "approved"
        $totalCashIn = TransaksiKas::where('kode', '101')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved')
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        $totalCashOut = TransaksiKas::where('kode', '102')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved')
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        // Hitung saldo kas berdasarkan tahun
        $saldoKas = $totalCashIn - $totalCashOut;

        // Ambil semua transaksi pada tahun tertentu dengan status approved
        $transaksiKas = TransaksiKas::where('perumahan_id', $perumahanId)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'approved')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'tanggal' => $transaksi->tanggal,
                    'keterangan_transaksi' => $transaksi->keterangan_transaksi,
                    'kode' => $transaksi->kode,
                    'jumlah' => number_format($transaksi->jumlah, 2, '.', ''), // Format angka
                    'keterangan_objek_transaksi' => $transaksi->keterangan_objek_transaksi ?? "-",
                    'metode_pembayaran' => $transaksi->metode_pembayaran ?? "Tunai",
                    'saldo_setelah_transaksi' => number_format($transaksi->saldo_setelah_transaksi ?? 0, 2, '.', ''),
                    'dibuat_oleh' => $transaksi->dibuat_oleh ?? "Admin",
                    'status' => $transaksi->status,
                ];
            });

        return response()->json([
            'tahun' => (string) $tahun,
            'totalCashIn' => number_format($totalCashIn, 2, '.', ''),
            'totalCashOut' => number_format($totalCashOut, 2, '.', ''),
            'saldoKas' => number_format($saldoKas, 2, '.', ''),
            'transaksiKas' => $transaksiKas,
        ]);
    }

}
