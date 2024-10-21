<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';

    protected $fillable = [
        'id_blok_unit',
        'id_user',
        'harga_jual_standar',
        'kelebihan_tanah',
        'penambahan_luas_bangunan',
        'perubahan_spek_bangunan',
        'total_harga_jual',
        'kpr_disetujui',
        'minimum_dp',
        'kewajiban_hutang',
    ];

    // Relasi ke user_perumahan
    public function userPerumahan()
    {
        return $this->belongsTo(UserPerumahan::class, 'id_user');
    }

    // Relasi ke blok_unit
    public function blokUnit()
    {
        return $this->belongsTo(BlokUnit::class, 'id_blok_unit');
    }
}
