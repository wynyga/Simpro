<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeramikFloor extends Model
{
    protected $table = 'keramik_floor';
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
        return 'KERM90-';
    }

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }
}

