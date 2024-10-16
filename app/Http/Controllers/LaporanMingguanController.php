<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanMingguan;

class LaporanMingguanController extends Controller
{
    // Method untuk menampilkan form input laporan mingguan
    public function create()
    {
        // Menampilkan halaman form input laporan mingguan
        return view('laporan_mingguan.create');
    }
    public function index()
    {
        // Mengambil semua data laporan mingguan dari database
        $laporan = LaporanMingguan::all();

        // Mengirimkan data laporan ke view 'laporan_mingguan.index'
        return view('laporan_mingguan.index', compact('laporan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'minggu_ke' => 'required|integer',
            'tahun_ke' => 'required|integer',
            'kategori' => 'required|string',
            'jumlah_transaksi' => 'required|numeric',
            'kas_project_choice' => 'required|string', // Menentukan sub-pilihan dari Kas Project
        ]);
    
        // Generate kode berdasarkan minggu dan tahun
        $code = LaporanMingguan::generateCode($request->minggu_ke, $request->tahun_ke);
    
        // Tentukan uraian dan code_account
        list($uraian, $kategori, $sub_kategori, $codeAccount) = $this->getUraianKategoriSubKategoriAndCodeAccount($request);
    
        // Simpan data transaksi
        LaporanMingguan::create([
            'minggu_ke' => $request->minggu_ke,
            'tahun_ke' => $request->tahun_ke,
            'code' => $code,
            'uraian' => $uraian,  // Gabungan Uraian > Kategori > Sub_Kategori
            'kategori' => $kategori,  // Kategori
            'sub_kategori' => $sub_kategori,  // Sub_Kategori
            'code_account' => $codeAccount,  // Kode Account berdasarkan uraian
            'total' => $request->jumlah_transaksi,
            'kategori' => $request->kategori,
        ]);
    
        return redirect()->route('laporan_mingguan.index')->with('success', 'Laporan Mingguan berhasil disimpan');
    }
    

    // Method untuk menentukan Uraian, Kategori, Sub_Kategori, dan Code Account
    private function getUraianKategoriSubKategoriAndCodeAccount(Request $request)
    {
        $mingguSebelumnya = $request->minggu_ke - 1;
        $tahun = $request->tahun_ke;

        // Tentukan uraian, kategori, sub_kategori, dan code_account berdasarkan pilihan user
        switch ($request->kas_project_choice) {
            case 'saldo_sisa':
                return [
                    "Saldo sisa Kas Proyek Minggu sebelumnya", 
                    null,  // Tidak ada kategori
                    null,  // Tidak ada sub_kategori
                    "M{$mingguSebelumnya}{$tahun}"
                ];
            case 'penerimaan_kas':
                switch ($request->penerimaan_kas_choice) {
                    case 'operasional_proyek':
                        if ($request->operasional_proyek_choice === 'booking_fee') {
                            return [
                                "Penerimaan Booking Fee", 
                                "Penerimaan dari Operasional Proyek",  // Kategori
                                null,  // Tidak ada sub_kategori
                                "KI0101M{$request->minggu_ke}{$tahun}"
                            ];
                        } elseif ($request->operasional_proyek_choice === 'down_payment') {
                            return [
                                "Penerimaan dari Down Payment", 
                                "Penerimaan dari Operasional Proyek",  // Kategori
                                null,  // Tidak ada sub_kategori
                                "KI0201M{$request->minggu_ke}{$tahun}"
                            ];
                        }
                        break;
                    case 'dana_tunai_lainnya':
                        switch ($request->dana_tunai_choice) {
                            case 'kelebihan_tanah':
                                return [
                                    "Biaya Kelebihan Tanah", 
                                    "Penerimaan dana Tuni lainnya",  // Kategori
                                    null,  // Tidak ada sub_kategori
                                    "KI0301M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'penambahan_spek':
                                return [
                                    "Biaya Penambahan Spek bangunan", 
                                    "Penerimaan dana Tuni lainnya",  // Kategori
                                    null,  // Tidak ada sub_kategori
                                    "KI0302M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'selisih_kpr':
                                return [
                                    "Biaya Selisih KPR", 
                                    "Penerimaan dana Tuni lainnya",  // Kategori
                                    null,  // Tidak ada sub_kategori
                                    "KI0303M{$request->minggu_ke}{$tahun}"
                                ];
                        }
                        break;
                    case 'penerimaan_kpr':
                        return [
                            "Penerimaan KPR", 
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "KI0401M{$request->minggu_ke}{$tahun}"
                        ];
                    case 'share_capital':
                        return [
                            "Share Capital Ordinary (Kantor Pusat / Modal Perseroan)", 
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "KI0501M{$request->minggu_ke}{$tahun}"
                        ];
                }
                break;
        }

        return [null, null, null, null];  // Return null if no match
    }
}

