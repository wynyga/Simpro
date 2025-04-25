<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    use HasFactory;

    protected $table = 'kwitansi';

    protected $fillable = [
        'transaksi_kas_id',
        'perumahan_id',
        'no_doc',
        'tanggal',
        'dari',
        'jumlah',
        'untuk_pembayaran',
        'jenis_penerimaan',
        'dibuat_oleh',
        'disetor_oleh',
        'mengetahui',
    ];

    public function transaksiKas()
    {
        return $this->belongsTo(TransaksiKas::class);
    }

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class);
    }
}

