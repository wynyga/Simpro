<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoofCeilingTile extends Model
{
    protected $table = 'roof_ceiling_tile';
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
        return 'RCT80-';
    }
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }
}

