<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayWork extends Model
{
    protected $table = 'day_work';
    protected $fillable = [
        'nama_barang',
        'uty',
        'satuan',
        'harga_satuan',
        'stock_bahan',
        'perumahan_id'
    ];
    protected static function booted()
    {
        static::creating(function ($dayWork) {
            // Mengenerate kode MDW10- dengan format yang sesuai
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;
            
            $dayWork->kode = 'MDW10-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }

    public function getPrefix()
    {
        return 'MDW10-';
    }

    // Relasi balik ke Perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

}

