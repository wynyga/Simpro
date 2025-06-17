<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangOut extends Model
{
    protected $table = 'gudang_out';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'tanggal',
        'peruntukan',
        'status',
        'jumlah',
        'satuan',
        'jumlah_harga',
        'keterangan',
        'perumahan_id'
    ];
    public function costTee()
    {
        return $this->belongsTo(\App\Models\CostTee::class, 'peruntukan');
    }
}
