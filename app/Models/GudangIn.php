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

    public function sttb()
    {
        return $this->hasOne(Sttb::class, 'gudang_in_id');
    }    
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class);
    }
    public function kwitansiCo()
    {
        return $this->hasOne(Kwitansi::class, 'gudang_in_id')->where('no_doc', 'like', '%/CO-%');
    }
    
}
