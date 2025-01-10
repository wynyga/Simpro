<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanBulanan;

class LaporanBulananController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bulan_ke' => 'required|integer',
            'tahun_ke' => 'required|integer',
            'jenis_biaya' => 'required|string',
            'uraian' => 'nullable|string',
            'kategori' => 'nullable|string',
            'sub_kategori' => 'nullable|string',
            'total' => 'nullable|numeric',
        ]);

        // Mengonversi bulan_ke dan tahun_ke menjadi code dengan memperbaiki format
        $codeBulanTahun = 'B' . $validatedData['bulan_ke'] . substr($validatedData['tahun_ke'], -2); // Bulan dua digit

        // Logika untuk prefix code berdasarkan jenis_biaya
        $prefix = null;
        switch ($validatedData['jenis_biaya']) {
            case 'JUMLAH KAS PROJECT / KAS MASUK BULAN INI':
                $prefix = 'KI';
                break;
            case 'JUMLAH KAS KELUAR BULAN INI':
                $prefix = 'KO';
                break;
            case 'SISA KAS PROJECT BULAN INI':
                $code_account = $codeBulanTahun;
                break;
            case 'JOURNAL':
            case 'MATERIAL INVENTORY':
                $code = null;
                break;
            default:
                return response()->json(['error' => 'Jenis biaya tidak valid'], 400);
        }
        if ($prefix) {
            $latestLaporan = LaporanBulanan::where('jenis_biaya', $validatedData['jenis_biaya'])
                                        ->latest('id')->first();
            $counter = $latestLaporan ? ((int) substr($latestLaporan->code_account, 2, 2) + 1) : 1;
            $code_account = $prefix . sprintf('%02d', $counter) . $codeBulanTahun;
        }

        // Membuat laporan baru
        $laporan = new LaporanBulanan();
        $laporan->bulan_ke = $validatedData['bulan_ke'];
        $laporan->tahun_ke = $validatedData['tahun_ke'];
        $laporan->code = $codeBulanTahun;
        $laporan->jenis_biaya = $validatedData['jenis_biaya'];
        $laporan->uraian = $validatedData['uraian'];
        $laporan->kategori = $validatedData['kategori']?? null;
        $laporan->sub_kategori = $validatedData['sub_kategori']?? null;
        $laporan->code_account = $code_account;
        $laporan->total = $validatedData['total'];
        $laporan->save();

        return response()->json($laporan, 201);
    }
}
