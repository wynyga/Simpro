<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandStoneSand extends Model
{
    protected $table = 'land_stone_sand';
    protected $fillable = [
        'nama_barang',
        'uty',
        'satuan',
        'harga_satuan',
        'stock_bahan',
        'perumahan_id'
    ];
    public function getPrefix()
    {
        return 'LSS40-';
    }

    // Relasi balik ke Perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

}
