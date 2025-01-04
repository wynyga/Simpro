<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlokUnit extends Model
{
    use HasFactory;

    protected $table = 'blok_unit';

    protected $fillable = [
        'id_tipe_rumah',
        'blok',
        'unit',
        'status',
        'perumahan_id'
    ];

    // Relasi ke tipe_rumah
    public function tipeRumah()
    {
        return $this->belongsTo(TipeRumah::class, 'id_tipe_rumah');
    }

    // Relasi ke transaksi
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_blok_unit');
    }

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }
}
