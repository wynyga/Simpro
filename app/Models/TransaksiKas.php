<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKas extends Model
{
    protected $table = 'transaksi_kas';

    protected $fillable = [
        'tanggal',
        'kode',
        'jumlah',
        'status',
        'saldo_setelah_transaksi',  
        'metode_pembayaran',        
        'dibuat_oleh',              
        'keterangan_objek_transaksi',
        'perumahan_id',
        'sumber_transaksi', 
        'keterangan_transaksi_id' 
    ];

    protected $casts = [
        'jumlah' => 'float',
        'saldo_setelah_transaksi' => 'float',
        'keterangan_transaksi_id' => 'float',
    ];

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

    public function kwitansi()
    {
        return $this->hasOne(Kwitansi::class, 'transaksi_kas_id');
    }

    public function costTee()
    {
        return $this->belongsTo(CostTee::class, 'keterangan_transaksi_id');
    }
}
