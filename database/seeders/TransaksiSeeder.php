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
        $harga_standar = 174350000;
        $booking_fee = 2000000;

        for ($i = 1; $i <= 10; $i++) {
            $kelebihan_tanah = rand(0, 5000000);
            $penambahan_luas_bangunan = rand(0, 10000000);
            $perubahan_spek_bangunan = rand(0, 5000000);

            $total_harga_jual = $harga_standar + $kelebihan_tanah + $penambahan_luas_bangunan + $perubahan_spek_bangunan;

            if ($i <= 5) {
                // Transaksi LUNAS
                $minimum_dp = $total_harga_jual;
                $plafon_kpr = 0;
                $kpr_disetujui = 'Tidak';
            } else {
                // Transaksi HUTANG
                $minimum_dp = (int)($total_harga_jual * 0.3);
                $plafon_kpr = $total_harga_jual - $minimum_dp;
                $kpr_disetujui = 'Ya';
            }

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

            // Buat transaksi kas & kwitansi baik untuk LUNAS maupun HUTANG
            $jumlah_pembayaran = $minimum_dp; // untuk LUNAS = full, HUTANG = DP

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

            $no_doc = KwitansiService::generateNoDoc(1, 'CI');

            Kwitansi::create([
                'transaksi_kas_id' => $kas->id,
                'perumahan_id' => 1,
                'no_doc' => $no_doc,
                'tanggal' => Carbon::now(),
                'dari' => $kas->dibuat_oleh,
                'jumlah' => $kas->jumlah,
                'untuk_pembayaran' => 'Pembayaran Penjualan Unit: ' . $transaksi->unit->nomor_unit,
                'metode_pembayaran' => $kas->metode_pembayaran,
                'dibuat_oleh' => 'Seeder',
                'disetor_oleh' => $kas->dibuat_oleh,
                'mengetahui' => null,
                'gudang_in_id' => null,
            ]);
        }
    }
}
