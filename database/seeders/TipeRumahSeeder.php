<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipeRumah;

class TipeRumahSeeder extends Seeder
{
    public function run()
    {
        TipeRumah::create([
            'tipe_rumah' => 'Meranti',
            'luas_bangunan' => 36,
            'luas_kavling' => 108,
            'harga_standar_tengah' => 174350000,
            'harga_standar_sudut' => 176350000,
            'penambahan_bangunan' => 3500000,
            'perumahan_id' => 1,
        ]);
    }
}