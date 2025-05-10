<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blok;

class BlokSeeder extends Seeder
{
    public function run()
    {
        $blokNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($blokNames as $nama) {
            Blok::create([
                'nama_blok' => $nama,
                'perumahan_id' => 1,
            ]);
        }
    }
}
