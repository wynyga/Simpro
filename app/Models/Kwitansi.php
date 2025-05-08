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
        'gudang_in_id',
        'mengetahui',
    ];

    public function transaksiKas()
    {
        return $this->belongsTo(TransaksiKas::class,'transaksi_kas_id');
    }

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class);
    }
    public function gudangIn()
    {
        return $this->belongsTo(GudangIn::class, 'gudang_in_id');
    }

}

