<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeRumah extends Model
{
    use HasFactory;
    protected $table = 'tipe_rumah';

    protected $fillable = [
        'id_perumahan',
        'tipe_rumah',
        'luas_bangunan',
        'luas_kavling',
        'harga_standar_tengah',
        'harga_standar_sudut',
        'penambahan_bangunan'
    ];

    // Relasi ke perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'id_perumahan');
    }

    // Relasi ke blok_unit
    public function blokUnit()
    {
        return $this->hasMany(BlokUnit::class, 'id_tipe_rumah');
    }
}
