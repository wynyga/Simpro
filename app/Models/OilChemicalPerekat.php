<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OilChemicalPerekat extends Model
{
    protected $table = 'oil_chemical_perekat';
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
        static::creating(function ($oilChemicalPerekat) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $oilChemicalPerekat->kode = 'OCP110-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
    public function getPrefix()
    {
        return 'OCP110-';
    }

    // Relasi balik ke Perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

}

