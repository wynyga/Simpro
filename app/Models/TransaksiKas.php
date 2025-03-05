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
        'status',
        'keterangan_objek_transaksi',
        'perumahan_id'
    ];

    // Relasi balik ke Perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }
}
