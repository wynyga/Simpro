<?php

namespace App\Http\Controllers;

use App\Models\LapBulanan;
use App\Models\CostStructure;
use Illuminate\Http\Request;

class LapBulananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $laporans = LapBulanan::whereHas('costStructure', function ($query) use ($user) {
            $query->where('perumahan_id', $user->perumahan_id);
        })->with('costStructure.costTee')->get();

        if ($laporans->isEmpty()) {
            return response()->json(['message' => 'Tidak ada laporan bulanan ditemukan'], 404);
        }

        return response()->json($laporans);
    }

    // Mendapatkan total kas masuk berdasarkan bulan dan tahun
    public function getKasMasuk($bulan, $tahun)
    {
        $user = auth()->user();
    
        // Hitung TOTAL Kas Masuk dari transaksi KASIN untuk bulan & tahun yang diminta, hanya untuk perumahan_id pengguna
        $totalKasMasuk = LapBulanan::whereHas('costStructure', function ($query) use ($user) {
            $query->where('cost_code', 'KASIN')
                  ->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulan)
          ->where('tahun', $tahun)
          ->sum('jumlah');
    
        // Hitung saldo kas dari bulan sebelumnya
        $bulanSebelumnya = $bulan - 1;
        $tahunSebelumnya = $tahun;
    
        if ($bulan == 1) { // Jika Januari, ambil saldo dari Desember tahun sebelumnya
            $bulanSebelumnya = 12;
            $tahunSebelumnya -= 1;
        }
    
        // Hitung saldo kas sebelumnya hanya untuk perumahan pengguna
        $saldoKasSebelumnya = LapBulanan::whereHas('costStructure', function ($query) use ($user) {
            $query->where('cost_code', 'KASIN')
                  ->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulanSebelumnya)
          ->where('tahun', $tahunSebelumnya)
          ->sum('jumlah');
    
        // Pastikan tidak ada saldo negatif, jika saldo sebelumnya tidak ada maka anggap 0
        $totalKasMasuk = $totalKasMasuk ?? 0;
        $saldoKasSebelumnya = $saldoKasSebelumnya ?? 0;
    
        // Formatkan Data untuk API Response
        return response()->json([
            'saldo_kas_sebelumnya' => [
                'code_account' => "B{$bulan}{$tahun}",
                'total_rp' => number_format($saldoKasSebelumnya, 2, ',', '.')
            ],
            'penerimaan_kas_bulan_ini' => [
                'code_account' => null, // Sudah ada di transaksi Kas Masuk
                'total_rp' => number_format($totalKasMasuk, 2, ',', '.')
            ],
            'total_kas_project' => [
                'total_rp' => number_format($saldoKasSebelumnya + $totalKasMasuk, 2, ',', '.')
            ]
        ]);
    }
    

    public function getHistory(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        // Ambil parameter filter dari request
        $status = $request->query('status'); // optional (approved, pending, rejected) - jika ada di laporan
        $bulan = $request->query('bulan'); // optional (format: 1-12)
        $tahun = $request->query('tahun'); // optional (format: YYYY)
        $perPage = $request->query('per_page', 10); // default: 10 item per page

        // Query laporan berdasarkan perumahan_id
        $query = LapBulanan::whereHas('costStructure', function ($query) use ($perumahanId) {
            $query->where('perumahan_id', $perumahanId);
        });

        // Filter berdasarkan status jika ada (opsional)
        if ($status) {
            $query->where('status', $status);
        }

        // Filter berdasarkan bulan jika diberikan
        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        // Filter berdasarkan tahun jika diberikan
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        // Ambil data dengan pagination
        $laporanBulanan = $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->paginate($perPage);

        return response()->json($laporanBulanan);
    }

    public function getKasKeluar($bulan, $tahun)
    {
        $user = auth()->user();
    
        // Hitung TOTAL Kas Keluar dari transaksi KASOUT untuk bulan & tahun yang diminta, hanya untuk perumahan pengguna
        $totalKasKeluar = LapBulanan::whereHas('costStructure', function ($query) use ($user) {
            $query->where('cost_code', 'KASOUT')
                  ->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulan)
          ->where('tahun', $tahun)
          ->sum('jumlah');
    
        // Ambil semua transaksi Kas Keluar yang terdaftar di laporan bulanan bulan ini, hanya untuk perumahan pengguna
        $laporanKasKeluar = LapBulanan::with('costStructure')
            ->whereHas('costStructure', function ($query) use ($user) {
                $query->where('cost_code', 'KASOUT')
                      ->where('perumahan_id', $user->perumahan_id);
            })
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
    
        // Pastikan tidak ada saldo negatif, jika total kas keluar tidak ada maka anggap 0
        $totalKasKeluar = $totalKasKeluar ?? 0;
    
        // Formatkan Data untuk API Response
        return response()->json([
            'total_kas_keluar' => [
                'total_rp' => number_format($totalKasKeluar, 2, ',', '.')
            ],
            'rincian_kas_keluar' => $laporanKasKeluar->map(function ($laporan) {
                return [
                    'id' => $laporan->id,
                    'code_account' => $laporan->code_account,
                    'kategori' => optional($laporan->costStructure)->description, // Hindari error jika NULL
                    'jumlah' => number_format($laporan->jumlah, 2, ',', '.'),
                    'created_at' => $laporan->created_at
                ];
            })
        ]);
    }
    

    // Menghitung sisa kas proyek
    public function getSisaKasProject($bulan, $tahun)
    {
        $user = auth()->user();

        $totalKasMasuk = LapBulanan::whereHas('costStructure', function ($query) use ($user) {
            $query->where('cost_code', 'KASIN')->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->sum('jumlah');

        $totalKasKeluar = LapBulanan::whereHas('costStructure', function ($query) use ($user) {
            $query->where('cost_code', 'KASOUT')->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->sum('jumlah');

        $sisaKas = $totalKasMasuk - $totalKasKeluar;

        return response()->json([
            'sisa_kas_project' => [
                'total_rp' => number_format($sisaKas, 2, ',', '.'),
                'status' => $sisaKas < 0 ? 'DEFISIT' : 'SURPLUS'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
    
        // Validasi input dari request
        $validated = $request->validate([
            'cost_structure_id' => 'required|exists:cost_structures,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric'
        ]);
    
        // Cari cost_structure berdasarkan ID yang dikirim user
        $costStructure = CostStructure::where('id', $validated['cost_structure_id'])->first();
    
        // Jika cost_structure_id tidak ditemukan, return error 404
        if (!$costStructure) {
            return response()->json(['error' => 'Cost Structure dengan ID tersebut tidak ditemukan.'], 404);
        }
    
        // Pastikan cost_structure milik perumahan pengguna
        if ($costStructure->perumahan_id !== $user->perumahan_id) {
            return response()->json(['error' => 'Unauthorized: Cost Structure bukan milik perumahan Anda.'], 403);
        }
    
        // Tambahkan perumahan_id sebelum insert ke database
        $validated['perumahan_id'] = $user->perumahan_id;
    
        // Simpan laporan bulanan baru
        $laporan = LapBulanan::create($validated);
    
        return response()->json([
            'message' => 'Laporan bulanan berhasil disimpan',
            'data' => $laporan
        ], 201);
    }
    
}


