<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sttb extends Model
{
    protected $table = 'sttb';

    protected $fillable = [
        'gudang_in_id',
        'perumahan_id',
        'no_doc',
        'tanggal',
        'nama_supplier',
        'nama_barang',
        'jumlah',
        'satuan',
        'diterima_oleh',
        'diserahkan_oleh',
        'mengetahui',
    ];
    protected $casts = [
        'jumlah' => 'float'
    ];    

    public function gudangIn()
    {
        return $this->belongsTo(GudangIn::class, 'gudang_in_id');
    }

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class);
    }
}
