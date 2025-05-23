<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rebar extends Model
{
    protected $table = 'rebar';
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
        return 'REB30-';
    }
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }
}

