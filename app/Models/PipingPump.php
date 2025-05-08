<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipingPump extends Model
{
    protected $table = 'piping_pump';
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
        return 'PIPP130-';
    }

    // Relasi balik ke Perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

}

