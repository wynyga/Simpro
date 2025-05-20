<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\TransaksiKas;
use App\Models\Kwitansi;
use App\Helpers\KwitansiService;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        $harga_standar = 174_350_000;
        $booking_fee = 2_000_000;

        for ($i = 1; $i <= 10; $i++) {
            // Hitung komponen harga tambahan
            $kelebihan_tanah = rand(0, 5_000_000);
            $penambahan_luas_bangunan = rand(0, 10_000_000);
            $perubahan_spek_bangunan = rand(0, 5_000_000);

            $total_harga_jual = $harga_standar + $kelebihan_tanah + $penambahan_luas_bangunan + $perubahan_spek_bangunan;

            // Penentuan status: LUNAS untuk 5 transaksi pertama
            $isLunas = $i <= 5;

            $minimum_dp = $isLunas
                ? $total_harga_jual      // Jika lunas, DP = total harga jual
                : (int) ($total_harga_jual * 0.3); // Jika hutang, DP hanya 30%

            $plafon_kpr = $isLunas
                ? 0                     // Tidak ada KPR kalau lunas
                : $total_harga_jual - $minimum_dp;

            $kpr_disetujui = $isLunas ? 'Tidak' : 'Ya';

            // Buat data transaksi
            $transaksi = Transaksi::create([
                'unit_id' => $i,
                'user_id' => $i,
                'harga_jual_standar' => $harga_standar,
                'kelebihan_tanah' => $kelebihan_tanah,
                'penambahan_luas_bangunan' => $penambahan_luas_bangunan,
                'perubahan_spek_bangunan' => $perubahan_spek_bangunan,
                'total_harga_jual' => $total_harga_jual,
                'minimum_dp' => $minimum_dp,
                'plafon_kpr' => $plafon_kpr,
                'kpr_disetujui' => $kpr_disetujui,
                'biaya_booking' => $booking_fee,
                'perumahan_id' => 1,
            ]);

            // Tentukan jumlah yang akan dibayarkan di TransaksiKas
            $jumlah_pembayaran = $isLunas
                ? $total_harga_jual      // Bayar full
                : $minimum_dp;           // Hanya bayar DP

            // Buat transaksi kas
            $kas = TransaksiKas::create([
                'tanggal' => Carbon::now(),
                'kode' => '101',
                'jumlah' => $jumlah_pembayaran,
                'saldo_setelah_transaksi' => null,
                'metode_pembayaran' => 'Cash',
                'dibuat_oleh' => 'Seeder',
                'keterangan_objek_transaksi' => 'Pembayaran Penjualan Unit: ' . $transaksi->unit->nomor_unit,
                'perumahan_id' => 1,
                'status' => 'approved',
                'sumber_transaksi' => 'penjualan',
                'keterangan_transaksi_id' => $transaksi->id,
            ]);

            // Buat kwitansi atas transaksi kas tersebut
            $no_doc = KwitansiService::generateNoDoc(1, 'CI');

            Kwitansi::create([
                'transaksi_kas_id' => $kas->id,
                'perumahan_id' => 1,
                'no_doc' => $no_doc,
                'tanggal' => $kas->tanggal,
                'dari' => $kas->dibuat_oleh,
                'jumlah' => $kas->jumlah,
                'untuk_pembayaran' => $kas->keterangan_objek_transaksi,
                'metode_pembayaran' => $kas->metode_pembayaran,
                'dibuat_oleh' => $kas->dibuat_oleh,
                'disetor_oleh' => $kas->dibuat_oleh,
                'mengetahui' => null,
                'gudang_in_id' => null,
            ]);
        }
    }
}
