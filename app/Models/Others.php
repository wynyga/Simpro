<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Others extends Model
{
    protected $table = 'others';
    protected $fillable = [
        'nama_barang',
        'uty',
        'satuan',
        'harga_satuan',
        'stock_bahan',
    ];
    protected static function booted()
    {
        static::creating(function ($others) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $others->kode = 'OTH110-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
    public function getPrefix()
    {
        return 'OTH110-';
    }

}

