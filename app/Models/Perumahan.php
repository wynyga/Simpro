<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perumahan extends Model
{
    use HasFactory;
    protected $table = 'perumahan';

    protected $fillable = [
        'nama_perumahan',
        'lokasi',
        'tanggal_harga'
    ];

    // Relasi ke tipe_rumah
    public function tipeRumah()
    {
        return $this->hasMany(TipeRumah::class, 'id_perumahan');
    }
}
