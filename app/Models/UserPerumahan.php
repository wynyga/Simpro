<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPerumahan extends Model
{
    use HasFactory;

    protected $table = 'user_perumahan';

    protected $fillable = [
        'nama_user',
        'alamat_user',
        'no_telepon',
    ];

    // Relasi ke transaksi
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_user');
    }
}
