<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKas extends Model
{
    protected $table = 'transaksi_kas';

    protected $fillable = [
        'tanggal',
        'keterangan_transaksi',
        'kode',
        'jumlah',
        'keterangan_objek_transaksi'
    ];
}
