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
        //dd($request->all());
        // Validasi input
        $request->validate([
            'minggu_ke' => 'required|integer',
            'tahun_ke' => 'required|integer',
            'jenis_biaya' => 'required|string',
            'uraian' => 'required|string',
            'kategori' => 'sometimes|string|nullable',
            'sub_kategori' => 'sometimes|string|nullable',
            'total' => 'required|numeric',  // Sesuaikan dengan input dari form
            'deskripsi' => 'nullable|string',
        ]);
    
        // Generate kode berdasarkan minggu dan tahun
        $code = LaporanMingguan::generateCode($request->minggu_ke, $request->tahun_ke);
    
        // Tentukan uraian, kategori, sub_kategori, code_account, dan jenis_biaya
        list($uraian, $kategori, $sub_kategori, $code_account) = $this->getUraianKategoriSubKategoriAndCodeAccount($request);
        //dd($uraian, $kategori, $sub_kategori, $code_account);
        // Simpan data transaksi
        LaporanMingguan::create([
            'minggu_ke' => $request->minggu_ke,
            'tahun_ke' => $request->tahun_ke,
            'code' => $code,
            'jenis_biaya' => $request->jenis_biaya,  // Jenis Biaya baru
            'uraian' => $uraian,  // Gabungan Uraian > Kategori > Sub_Kategori
            'kategori' => $kategori,  // Kategori
            'sub_kategori' => $sub_kategori,  // Sub_Kategori
            'code_account' => $code_account,  // Kode Account berdasarkan uraian
            'total' => $request->total,  // Gunakan 'total' yang sesuai dengan form
            'deskripsi' => $request->deskripsi,  // Gunakan 'deskripsi' sesuai form
        ]);
    
        return redirect()->route('laporan_mingguan.index')->with('success', 'Laporan Mingguan berhasil disimpan');
    }
    

    // Method untuk menentukan Uraian, Kategori, Sub_Kategori, dan Code Account

    private function getUraianKategoriSubKategoriAndCodeAccount(Request $request)
    {
        $mingguSebelumnya = $request->minggu_ke - 1;
        $mingguIni = $request->minggu_ke;
        $tahun = $request->tahun_ke;
    
        // Tentukan uraian, kategori, sub_kategori, dan code_account berdasarkan pilihan user
        switch ($request->jenis_biaya){
            case 'KAS PROJECT / KAS MASUK MINGGU INI':
                switch ($request->uraian) {
                    case 'saldo_sisa':
                        return [
                            "Saldo sisa Kas Proyek Minggu sebelumnya", 
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "M{$mingguSebelumnya}{$tahun}"  // Code Account
                        ];
                    case 'penerimaan_kas':
                        switch ($request->kategori) {
                            case 'operasional_proyek':
                                if ($request->sub_kategori === 'booking_fee') {
                                    return [
                                        "Penerimaan Booking Fee", 
                                        "Penerimaan dari Operasional Proyek",  // Kategori
                                        "Booking Fee",  // Sub Kategori
                                        "KI0101M{$request->minggu_ke}{$tahun}"  // Code Account
                                    ];
                                } elseif ($request->sub_kategori === 'down_payment') {
                                    return [
                                        "Penerimaan dari Down Payment", 
                                        "Penerimaan dari Operasional Proyek",  // Kategori
                                        "Down Payment",  // Sub Kategori
                                        "KI0201M{$request->minggu_ke}{$tahun}"  // Code Account
                                    ];
                                }
                                break;
                            case 'dana_tunai_lainnya':
                                switch ($request->sub_kategori) {
                                    case 'kelebihan_tanah':
                                        return [
                                            "Biaya Kelebihan Tanah", 
                                            "Penerimaan dana Tunai lainnya",  // Kategori
                                            "Kelebihan Tanah",  // Sub Kategori
                                            "KI0301M{$request->minggu_ke}{$tahun}"  // Code Account
                                        ];
                                    case 'penambahan_spek':
                                        return [
                                            "Biaya Penambahan Spek bangunan", 
                                            "Penerimaan dana Tuni lainnya",  // Kategori
                                            "Penambahan Spek",  // Sub Kategori
                                            "KI0302M{$request->minggu_ke}{$tahun}"  // Code Account
                                        ];
                                    case 'selisih_kpr':
                                        return [
                                            "Biaya Selisih KPR", 
                                            "Penerimaan dana Tuni lainnya",  // Kategori
                                            "Selisih KPR",  // Sub Kategori
                                            "KI0303M{$request->minggu_ke}{$tahun}"  // Code Account
                                        ];
                                }
                            
                            case 'penerimaan_kpr':
                                return[
                                    "Penerimaan KPR",
                                    null,
                                    null,
                                    "KI0401M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'share_capital':
                                return[
                                    "Share Capital Ordinary (Kantor Pusat / Modal Perseroan)",
                                    null,
                                    null,
                                    "KI0501M{$request->minggu_ke}{$tahun}"
                                ];
                            break;
                        }
                }
            break;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            case 'KAS KELUAR MINGGU INI':
                switch ($request->uraian) {
                    case 'pembiayaan_project':
                        switch ($request->kategori) {
                            case 'biaya_tanah':
                                return [
                                    "Pembiayaan Project", 
                                    "Biaya Tanah dan Making Tanah", 
                                    null,  // Kode Akun
                                    "KO0101M{$request->minggu_ke}{$tahun}" // Code Account
                                ];
                            case 'biaya_prasarana':
                                return [
                                    "Pembiayaan Project", 
                                    "Biaya Prasarana Jalan, Drainase, Listrik, air dll", 
                                    null,  
                                    "KO0102M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'biaya_sarana':
                                return [
                                    "Pembiayaan Project", 
                                    "Biaya Sarana bangunan; (Biaya KPR, IMB, Listrik, PDAM, AJB, Sertifikat)", 
                                    null,  
                                    "KO0103M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'biaya_konstuksi':
                                return[
                                    "Pembiayaan Project",
                                    "Biaya Konstuksi Bangunan",
                                    null,  
                                    "KO0104M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'biaya_marketing':
                                return[
                                    "Pembiayaan Project",
                                    "Biaya Marketing (Iklan, Brosur, Marketing Fee, dll)",
                                    null,  
                                    "KO0105M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'biaya_pajak':
                                return[
                                    "Pembiayaan Project",
                                    "Pajak Bayar Dimuka ( PNBP, BPHTB, PPh Pasal 23,PPh Pasal 21 dan PPN )",
                                    null,  
                                    "KO0106M{$request->minggu_ke}{$tahun}"                                    
                                ];
                            case 'biaya_lainnya':
                                return[
                                    "Pembiayaan Project",
                                    "Biaya Lainnya yang berhubungan dengan proyek",
                                    null,  
                                    "KO0107M{$request->minggu_ke}{$tahun}"                                    
                                ];   
                            case 'upah_buruh':
                                return[
                                    "Pembiayaan Project",
                                    "Upah Buruh Kerja Harian Lepas",
                                    null,  
                                    "KO0108M{$request->minggu_ke}{$tahun}"                                    
                                ];                                                                
                        }
                        break;
    
                    case 'pembiayaan_personalia':
                        switch ($request->kategori) {
                            case 'biaya_personalia':
                                return [
                                    "Pembiayaan Personalia, Administrasi dan Operasional Kantor", 
                                    "Biaya Personalia", 
                                    null,  
                                    "KO0201M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'biaya_operasional':
                                return [
                                    "Pembiayaan Personalia, Administrasi dan Operasional Kantor", 
                                    "Biaya Operasional Kantor", 
                                    null,  
                                    "KO0202M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'pengadaan_asset':
                                return [
                                    "Pembiayaan Personalia, Administrasi dan Operasional Kantor", 
                                    "Pengadaan Asset Kantor / Proyek", 
                                    null,  
                                    "KO0203M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'biaya_lain':
                                return [
                                    "Pembiayaan Personalia, Administrasi dan Operasional Kantor", 
                                    "Biaya Lain - lain yang berhubungan dgn operasional", 
                                    null,  
                                    "KO0204M{$request->minggu_ke}{$tahun}"
                                ];   
                        }
                        break;
    
                    case 'biaya_pinjaman':
                        switch ($request->kategori) {
                            case 'biaya_administrasi_pinjaman':
                                return [
                                    "Biaya Pinjaman / Pengembalian Pinjaman", 
                                    "Biaya Administrasi Pinjaman (Notaris, Adm, Aprisal dll)", 
                                    null,  
                                    "KO0301M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'pembayaran_bunga':
                                return [
                                    "Biaya Pinjaman / Pengembalian Pinjaman", 
                                    "Pembayaran Bunga Pinjaman 12,5 % per Tahun", 
                                    null,  
                                    "KO0302M{$request->minggu_ke}{$tahun}"
                                ];
                            case 'pengembalian_pokok':
                                return [
                                    "Biaya Pinjaman / Pengembalian Pinjaman", 
                                    "Pengembalian Pokok Pinjaman Bank (Pot. KPR)", 
                                    null,  
                                    "KO0303M{$request->minggu_ke}{$tahun}"
                                ];   
                            case 'pengembalian_pinjaman':
                                return [
                                    "Biaya Pinjaman / Pengembalian Pinjaman", 
                                    "Pengembalian Pinjaman Pihak Ketiga", 
                                    null,  
                                    "KO0304M{$request->minggu_ke}{$tahun}"
                                ];     
                            case 'biaya_pengembalian':
                                return [
                                    "Biaya Pinjaman / Pengembalian Pinjaman", 
                                    "Biaya Pengembalian Uang Muka", 
                                    null,  
                                    "KO0305M{$request->minggu_ke}{$tahun}"
                                ];                      
                            
                        }
                        break;
    
                    case 'setor_kantor_pusat':
                        return [
                            "Setor ke Kantor Pusat", 
                            null,  // Tidak ada kategori
                            null,  
                            "KO0401M{$request->minggu_ke}{$tahun}"
                        ];
    
                    case 'bahan_material_proyek':
                        return [
                            "Bahan / Material Proyek Telah dibayarkan minggu ini", 
                            null,  
                            null,  
                            "M{$request->minggu_ke}{$tahun}"
                        ];
    
                    case 'pembayaran_material_jatuh_tempo':
                        $mingguDepan = $mingguIni + 1;  // Minggu berikutnya
                        return [
                            "Pembayaran Material / Bahan Jatuh Tempo (Minggu Depan)", 
                            null,  
                            null,
                            "M{$mingguDepan}{$tahun}"
                        ];
    
                    // Tambahkan logika untuk uraian lainnya jika diperlukan
                }
            break;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            case 'SISA KAS PROJECT MINGGU INI':
                return[
                    "Sisa Kas Project Minggu Ini",
                    null,
                    null,
                    null,
                ];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
            case 'HUTANG MATERIAL DAN RENCANA KEBUTUHAN':
                switch ($request->uraian) {
                    case 'jumlah_hutang':
                        return [
                            "Jumlah Hutang Bahan / Material Proyek ke Vendor",  // Uraian
                            null,                                              // Tidak ada kategori
                            null,                                              // Tidak ada sub_kategori
                            "M{$request->minggu_ke}{$tahun}"                    // Code Account
                        ];
                    
                    case 'pembayaran_jatuh_tempo_1_minggu':
                        $mingguDepan = $mingguIni + 1;
                        return [
                            "Pembayaran Bahan/Material Proyek Jatuh Tempo (1 Minggu Berikut)",  // Uraian
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "M{$mingguDepan}{$tahun}"  // Code Account untuk 1 minggu ke depan
                        ];
                    
                    case 'pembayaran_jatuh_tempo_2_minggu':
                        $mingguDepan = $mingguIni + 2;
                        return [
                            "Pembayaran Bahan/Material Proyek Jatuh Tempo (2 Minggu Berikut)",  // Uraian
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "M{$mingguDepan}{$tahun}"  // Code Account untuk 2 minggu ke depan
                        ];

                    case 'pembayaran_jatuh_tempo_3_minggu':
                        $mingguDepan = $mingguIni + 3;
                        return [
                            "Pembayaran Bahan/Material Proyek Jatuh Tempo (3 Minggu Berikut)",  // Uraian
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "M{$mingguDepan}{$tahun}"  // Code Account untuk 3 minggu ke depan
                        ];

                    case 'pembayaran_jatuh_tempo_bulan':
                        return [
                            "Pembayaran Bahan/Material Proyek Jatuh Tempo (Bulan Berikut)",  // Uraian
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "M10{$tahun}"  // Code Account untuk bulan berikutnya (asumsi bulan adalah ke-10 dari contoh)
                        ];

                    case 'rencana_pembelian_minggu_depan':
                        $mingguDepan = $mingguIni + 1;
                        return [
                            "Rencana Pembelian Bahan Proyek (Minggu Berikut)",  // Uraian
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "M{$mingguDepan}{$tahun}"  // Code Account untuk 1 minggu ke depan
                        ];
                }
            break;

        }
    
        return [null, null, null, null];  // Return null if no match
    }
    
    
}

