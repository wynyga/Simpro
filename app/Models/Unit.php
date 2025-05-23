<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';

    protected $fillable = [
        'blok_id',
        'tipe_rumah_id',
        'nomor_unit',
        'status' 
    ];

    public function blok()
    {
        return $this->belongsTo(Blok::class, 'blok_id');
    }

    public function tipeRumah()
    {
        return $this->belongsTo(TipeRumah::class, 'tipe_rumah_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_unit'); 
    }
}
