<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'tipe_log',
        'pesan'
    ];
}

