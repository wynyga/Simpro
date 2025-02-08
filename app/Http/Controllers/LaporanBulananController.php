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

    public function getParentCodeDetails(Request $request)
    {
        $request->validate([
            'parent_code_account' => 'required|string'
        ]);

        $parent_code_account = $request->parent_code_account;

        // Query untuk mendapatkan data berdasarkan parent_code_account
        $laporan = LaporanBulanan::where('code_account', $parent_code_account)->first();

        // Periksa apakah data ditemukan
        if (!$laporan) {
            return response()->json(['message' => 'No data found for the given code account'], 404);
        }

        // Cek apakah 'kategori' atau 'sub_kategori' null
        if ($laporan->kategori === null) {
            $laporan->input_required = 'kategori';
        } elseif ($laporan->sub_kategori === null) {
            $laporan->input_required = 'sub_kategori';
        } else {
            $laporan->input_required = 'none'; // Semua data sudah terisi
        }

        // Mengembalikan data yang ditemukan dengan info tambahan
        return response()->json($laporan);
    }

    public function addSubCategory(Request $request)
    {
        $validatedData = $request->validate([
            'parent_code_account' => 'required|string',
            'kategori' => 'nullable|string',
            'sub_kategori' => 'nullable|string',
            'total' => 'required|numeric',
        ]);
    
        $parent = LaporanBulanan::where('code_account', $validatedData['parent_code_account'])->first();
    
        if (!$parent) {
            return response()->json(['message' => 'Parent code account not found'], 404);
        }
    
        // Extract the prefix and base code from the parent_code_account
        $baseCodePattern = '/([A-Z]+)(\d+)(B\d+)/';
        preg_match($baseCodePattern, $parent->code_account, $matches);
        $prefix = $matches[1];
        $baseNumber = $matches[2];
        $yearCode = $matches[3];
    
        // Find the latest entry with a similar code_account pattern to increment the sequence
        $latestLaporan = LaporanBulanan::where('code_account', 'like', "{$prefix}{$baseNumber}%{$yearCode}")
                                       ->latest('id')->first();
        $latestNumber = $latestLaporan ? ((int) substr($latestLaporan->code_account, -3) + 1) : 1;
        $newCodeAccount = sprintf('%s%02d%02d%s', $prefix, $baseNumber, $latestNumber, $yearCode);
    
        // Create new entry based on parent data and new inputs
        $laporan = new LaporanBulanan([
            'bulan_ke' => $parent->bulan_ke,
            'tahun_ke' => $parent->tahun_ke,
            'code' => $parent->code,
            'jenis_biaya' => $parent->jenis_biaya,
            'uraian' => $parent->uraian,
            'kategori' => $validatedData['kategori'] ?? $parent->kategori,
            'sub_kategori' => $validatedData['sub_kategori'] ?? $parent->sub_kategori,
            'code_account' => $newCodeAccount,
            'total' => $validatedData['total']
        ]);
        $laporan->save();
    
        return response()->json($laporan, 201);
    }

    public function enhanceCodeAccount(Request $request)
    {
        $validatedData = $request->validate([
            'bulan_ke' => 'required|integer',
            'tahun_ke' => 'required|integer',
            'jenis_biaya' => 'required|string',
            'kategori' => 'required|string',
            'sub_kategori' => 'required|string'
        ]);
    
        // Mengonversi bulan_ke dan tahun_ke menjadi code dengan memperbaiki format
        $codeBulanTahun = 'B' . $validatedData['bulan_ke'] . substr($validatedData['tahun_ke'], -2);
    
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
                $prefix = null; // Handle cases where no increment is needed
                break;
            default:
                return response()->json(['error' => 'Jenis biaya tidak valid'], 400);
        }
        
        if ($prefix) {
            $latestLaporan = LaporanBulanan::where('jenis_biaya', $validatedData['jenis_biaya'])
                                           ->latest('id')->first();
            $counter = $latestLaporan ? ((int) substr($latestLaporan->code_account, 4, 2) + 1) : 1;
            $code_account = $prefix . sprintf('%02d', $counter) . $codeBulanTahun;
        }
    
        // Membuat laporan baru
        $laporan = new LaporanBulanan([
            'bulan_ke' => $validatedData['bulan_ke'],
            'tahun_ke' => $validatedData['tahun_ke'],
            'jenis_biaya' => $validatedData['jenis_biaya'],
            'kategori' => $validatedData['kategori'],
            'sub_kategori' => $validatedData['sub_kategori'],
            'code_account' => $code_account ?? $codeBulanTahun, // Handle cases where prefix is null
            'total' => $request->total ?? 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    
        $laporan->save();
    
        return response()->json($laporan, 201);
    }     
}
