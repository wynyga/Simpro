<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangIn extends Model
{
    protected $table = 'gudang_in';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'pengirim',
        'no_nota',
        'tanggal_barang_masuk',
        'sistem_pembayaran',
        'status',
        'jumlah',
        'satuan',
        'harga_satuan',
        'jumlah_harga',
        'keterangan',
        'perumahan_id'
    ];
}
