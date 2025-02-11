<?php

namespace App\Http\Controllers;

use App\Models\LapBulanan;
use Illuminate\Http\Request;

class LapBulananController extends Controller
{
    public function index()
    {
        $laporans = LapBulanan::with('costStructure.costTee')->get();
    
        $laporans = $laporans->map(function ($laporan) {
            return [
                'id' => $laporan->id,
                'cost_structure_id' => $laporan->cost_structure_id,
                'bulan' => $laporan->bulan,
                'tahun' => $laporan->tahun,
                'jumlah' => $laporan->jumlah,
                'created_at' => $laporan->created_at,
                'updated_at' => $laporan->updated_at,
                'code_account' => $laporan->code_account, // Tambahkan Code Account
                'cost_structure' => [
                    'id' => $laporan->costStructure->id,
                    'cost_tee_code' => $laporan->costStructure->cost_tee_code,
                    'cost_code' => $laporan->costStructure->cost_code,
                    'description' => $laporan->costStructure->description,
                    'created_at' => $laporan->costStructure->created_at,
                    'updated_at' => $laporan->costStructure->updated_at,
                    'cost_tee' => [
                        'id' => $laporan->costStructure->costTee->id,
                        'cost_tee_code' => $laporan->costStructure->costTee->cost_tee_code,
                        'cost_element_code' => $laporan->costStructure->costTee->cost_element_code,
                        'description' => $laporan->costStructure->costTee->description,
                        'created_at' => $laporan->costStructure->costTee->created_at,
                        'updated_at' => $laporan->costStructure->costTee->updated_at
                    ]
                ]
            ];
        });
    
        return response()->json($laporans);
    }

    public function getKasMasuk($bulan, $tahun)
    {
        // Hitung TOTAL Kas Masuk dari transaksi KASIN untuk bulan & tahun yang diminta
        $totalKasMasuk = LapBulanan::whereHas('costStructure', function ($query) {
            $query->where('cost_code', 'KASIN'); // Pastikan hanya mengambil Kas Masuk
        })->where('bulan', $bulan)
          ->where('tahun', $tahun)
          ->sum('jumlah');
    
        // Ambil saldo kas proyek dari bulan sebelumnya
        $bulanSebelumnya = $bulan - 1;
        $tahunSebelumnya = $tahun;
    
        if ($bulan == 1) { // Jika Januari, ambil saldo dari Desember tahun sebelumnya
            $bulanSebelumnya = 12;
            $tahunSebelumnya -= 1;
        }
    
        // Ambil total saldo kas masuk dari bulan sebelumnya
        $saldoKasSebelumnya = LapBulanan::whereHas('costStructure', function ($query) {
            $query->where('cost_code', 'KASIN'); // Hanya ambil transaksi Kas Masuk
        })->where('bulan', $bulanSebelumnya)
          ->where('tahun', $tahunSebelumnya)
          ->sum('jumlah');
    
        // 3️⃣ Formatkan Data untuk API Response
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
    

    public function getKasKeluar($bulan, $tahun)
    {
        // 1️⃣ Hitung TOTAL Kas Keluar dari transaksi KASOUT untuk bulan & tahun yang diminta
        $totalKasKeluar = LapBulanan::whereHas('costStructure', function ($query) {
            $query->where('cost_code', 'KASOUT'); // Pastikan hanya mengambil Kas Keluar
        })->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->sum('jumlah');

        // 2️⃣ Ambil semua transaksi Kas Keluar yang terdaftar di laporan bulanan bulan ini
        $laporanKasKeluar = LapBulanan::with('costStructure')
            ->whereHas('costStructure', function ($query) {
                $query->where('cost_code', 'KASOUT'); // Hanya transaksi Kas Keluar
            })
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        // 3️⃣ Formatkan Data untuk API Response
        return response()->json([
            'total_kas_keluar' => [
                'total_rp' => number_format($totalKasKeluar, 2, ',', '.')
            ],
            'rincian_kas_keluar' => $laporanKasKeluar->map(function ($laporan) {
                return [
                    'id' => $laporan->id,
                    'code_account' => $laporan->code_account,
                    'kategori' => $laporan->costStructure->description,
                    'jumlah' => number_format($laporan->jumlah, 2, ',', '.'),
                    'created_at' => $laporan->created_at
                ];
            })
        ]);
    }

    public function getSisaKasProject($bulan, $tahun)
    {
        // 1️⃣ Ambil Total Kas Masuk
        $totalKasMasuk = LapBulanan::whereHas('costStructure', function ($query) {
            $query->where('cost_code', 'KASIN'); // Hanya ambil Kas Masuk
        })->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->sum('jumlah');

        // 2️⃣ Ambil Total Kas Keluar
        $totalKasKeluar = LapBulanan::whereHas('costStructure', function ($query) {
            $query->where('cost_code', 'KASOUT'); // Hanya ambil Kas Keluar
        })->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->sum('jumlah');

        // 3️⃣ Hitung Sisa Kas
        $sisaKas = $totalKasMasuk - $totalKasKeluar;

        // 4️⃣ Format Data untuk API Response
        return response()->json([
            'sisa_kas_project' => [
                'total_rp' => number_format($sisaKas, 2, ',', '.'),
                'status' => $sisaKas < 0 ? 'DEFISIT' : 'SURPLUS',
                'color' => $sisaKas < 0 ? 'red' : 'black'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_structure_id' => 'required|exists:cost_structures,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric'
        ]);
    
        $laporan = LapBulanan::create($validated);
    
        return response()->json([
            'message' => 'Laporan bulanan berhasil disimpan',
            'data' => $laporan
        ], 201);
    }
    
}


