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
        'saldo_setelah_transaksi',  
        'metode_pembayaran',        
        'dibuat_oleh',              
        'keterangan_objek_transaksi',
        'perumahan_id'
    ];

    // Pastikan nilai jumlah dan saldo selalu dalam bentuk float
    protected $casts = [
        'jumlah' => 'float',
        'saldo_setelah_transaksi' => 'float',
    ];

    // Relasi balik ke Perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

        public function kwitansi()
    {
        return $this->hasOne(Kwitansi::class, 'transaksi_kas_id');
    }

}
