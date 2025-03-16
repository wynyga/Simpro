<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeRumah extends Model
{
    use HasFactory;
    protected $table = 'tipe_rumah';

    protected $fillable = [
        'perumahan_id',
        'tipe_rumah',
        'luas_bangunan',
        'luas_kavling',
        'harga_standar_tengah',
        'harga_standar_sudut',
        'penambahan_bangunan'
    ];

    // Relasi ke blok_unit
    public function blokUnit()
    {
        return $this->hasMany(Blok::class, 'id_tipe_rumah');
    }

    protected $casts = [
        'harga_standar_tengah' => 'float',
        'harga_standar_sudut' => 'float',
        'penambahan_bangunan' => 'float',
    ];

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }
}
