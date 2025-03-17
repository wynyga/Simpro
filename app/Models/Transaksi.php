<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'unit_id',  // Updated to reflect new structure
        'user_id',
        'harga_jual_standar',
        'kelebihan_tanah',
        'penambahan_luas_bangunan',
        'perubahan_spek_bangunan',
        'total_harga_jual',
        'kpr_disetujui',
        'minimum_dp',
        'kewajiban_hutang',
        'perumahan_id'  // Ensure this is maintained if still relevant
    ];

    // Relationship to Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    // Relationship to UserPerumahan
    public function userPerumahan()
    {
        return $this->belongsTo(UserPerumahan::class, 'user_id');
    }

    // Optional: Relationship to Perumahan if you have a separate Perumahan entity
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
        'kewajiban_hutang' => 'float',
    ];
}
