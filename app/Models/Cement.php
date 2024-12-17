<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cement extends Model
{
    protected $table = 'cement';
    protected $fillable = [
        'nama_barang',
        'uty',
        'satuan',
        'harga_satuan',
        'stock_bahan',
    ];
    protected static function booted()
    {
        static::creating(function ($cement) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $cement->kode = 'CEM50-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
    public function getPrefix()
    {
        return 'CEM50-';
    }
}

