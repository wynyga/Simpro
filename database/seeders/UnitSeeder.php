<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blok;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $tipeRumahId = 1; // pastikan ini adalah ID tipe rumah "Meranti"
        $blokList = Blok::whereIn('nama_blok', ['A', 'B', 'C', 'D', 'E'])
                        ->where('perumahan_id', 1)
                        ->get();

        foreach ($blokList as $blok) {
            for ($i = 1; $i <= 5; $i++) {
                Unit::create([
                    'blok_id' => $blok->id,
                    'tipe_rumah_id' => $tipeRumahId,
                    'nomor_unit' => "{$blok->nama_blok}-0{$i}",
                ]);
            }
        }
    }
}
