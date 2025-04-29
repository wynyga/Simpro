<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'unit_id',
        'user_id',
        'harga_jual_standar',
        'kelebihan_tanah',
        'penambahan_luas_bangunan',
        'perubahan_spek_bangunan',
        'total_harga_jual',
        'kpr_disetujui',
        'minimum_dp',
        'plafon_kpr',
        'biaya_booking',
        'perumahan_id'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function userPerumahan()
    {
        return $this->belongsTo(UserPerumahan::class, 'user_id');
    }

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

    protected $casts = [
        'harga_jual_standar' => 'float',
        'kelebihan_tanah' => 'float',
        'penambahan_luas_bangunan' => 'float',
        'perubahan_spek_bangunan' => 'float',
        'total_harga_jual' => 'float',
        'minimum_dp' => 'float',
        'plafon_kpr' => 'float',
        'biaya_booking' => 'float',
        
    ];
}
