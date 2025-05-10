<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CostElement;

class CostElementSeeder extends Seeder
{
    public function run()
    {
        $elements = [
            ['code' => 'KI0101', 'desc' => 'Penerimaan Booking Fee'],
            ['code' => 'KI0201', 'desc' => 'Penerimaan dari Down Payment'],
            ['code' => 'KI0301', 'desc' => 'Biaya Kelebihan Tanah'],
            ['code' => 'KI0302', 'desc' => 'Biaya Penambahan Spek bangunan'],
            ['code' => 'KI0303', 'desc' => 'Biaya Selisih KPR'],
            ['code' => 'KI0401', 'desc' => 'Penerimaan KPR'],
            ['code' => 'KI0501', 'desc' => 'Share Capital Ordinary (Kantor Pusat / Modal Perseroan)'],
            ['code' => 'KI0601', 'desc' => 'Penerimaan dari Penjulan Barang dan Jasa Kerja'],
            ['code' => 'KI0602', 'desc' => 'Penerimaan dari Bunga Bank'],
            ['code' => 'KI0701', 'desc' => 'Pencairan Piutang Retensi Bestek (IMB, Listrik, Air dan Jalan)'],
            ['code' => 'KI0702', 'desc' => 'Pelunasan Piutang DP dan Selisih DP (Down Payment)'],
            ['code' => 'KI0703', 'desc' => 'Pelunasan Piutang Biaya Kelebihan Tanah'],
            ['code' => 'KI0704', 'desc' => 'Pelunasan Piutang Biaya Penambahan Spek Bangunan'],
            ['code' => 'KI0705', 'desc' => 'Pelunasan Piutang Selisih KPR'],
            ['code' => 'KI0706', 'desc' => 'Pelunasan Piutang Berelasi'],
            ['code' => 'KI0707', 'desc' => 'Pelunasan Piutang Pihak Ketiga'],
            ['code' => 'KI0901', 'desc' => 'Hutang Bank (Pencairan Dana Bank)'],
            ['code' => 'KI1001', 'desc' => 'Hutang Pihak Ketiga (Pinjaman Pihak Ketiga)'],
            ['code' => 'KI1101', 'desc' => 'Hutang Berelasi (Setoran dari Mitra Relasi atau Pemegang Saham)'],

            ['code' => 'KO0105', 'desc' => 'Biaya Marketing (Iklan, Brosur, Marketing Fee, dll)'],
            ['code' => 'KO0106', 'desc' => 'Pajak Bayar Dimuka (PNBP, BPHTB, PPh 23, PPh 21, PPN)'],
            ['code' => 'KO0107', 'desc' => 'Biaya Lainnya yang berhubungan dengan proyek'],
            ['code' => 'KO0108', 'desc' => 'Upah Buruh Kerja Harian Lepas'],
            ['code' => 'KO0201', 'desc' => 'Biaya Personalia'],
            ['code' => 'KO0202', 'desc' => 'Biaya Operasional Kantor'],
            ['code' => 'KO0203', 'desc' => 'Pengadaan Asset Kantor / Proyek'],
            ['code' => 'KO0204', 'desc' => 'Biaya Lain - lain yang berhubungan dgn operasional'],
            ['code' => 'KO0301', 'desc' => 'Biaya Administrasi Pinjaman (Notaris, Adm, Aprisal dll)'],
            ['code' => 'KO0302', 'desc' => 'Pembayaran Bunga Pinjaman 12,5 % per Tahun'],
            ['code' => 'KO0303', 'desc' => 'Pengembalian Pokok Pinjaman Bank (Pot. KPR)'],
            ['code' => 'KO0304', 'desc' => 'Pengembalian Pinjaman Pihak Ketiga / Berelasi'],
            ['code' => 'KO0305', 'desc' => 'Biaya Pengembalian Uang Muka'],
            ['code' => 'KO0401', 'desc' => 'Piutang (Dana Ditahan) Retensi Bestek'],
            ['code' => 'KO0402', 'desc' => 'Piutang DP dan Selisih DP'],
            ['code' => 'KO0403', 'desc' => 'Piutang Biaya Kelebihan Tanah'],
            ['code' => 'KO0404', 'desc' => 'Piutang Biaya Penambahan Spek Bangunan'],
            ['code' => 'KO0405', 'desc' => 'Piutang Selisih KPR'],
            ['code' => 'KO0406', 'desc' => 'Piutang Berelasi'],
            ['code' => 'KO0407', 'desc' => 'Piutang Pihak Ketiga'],
            ['code' => 'KO0501', 'desc' => 'Setor ke Kantor Pusat'],
        ];

        foreach ($elements as $item) {
            CostElement::create([
                'perumahan_id' => 1,
                'cost_element_code' => $item['code'],
                'cost_centre_code' => substr($item['code'], 0, 5), // ← relasi otomatis
                'description' => $item['desc'],
            ]);
        }
    }
}
