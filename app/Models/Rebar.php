<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rebar extends Model
{
    protected $table = 'rebar';
    protected $fillable = [
        'nama_barang',
        'uty',
        'satuan',
        'harga_satuan',
        'stock_bahan',
    ];
    protected static function booted()
    {
        static::creating(function ($rebar) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $rebar->kode = 'REB30-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
    public function getPrefix()
    {
        return 'REB30-';
    }
}

