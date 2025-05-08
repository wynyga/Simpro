<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayWork extends Model
{
    protected $table = 'day_work';
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
        return 'MDW10-';
    }

    // Relasi balik ke Perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

}

