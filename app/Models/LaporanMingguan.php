<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanMingguan extends Model
{
    protected $table = 'laporan_mingguan';

    protected $fillable = [
        'minggu_ke',
        'tahun_ke',
        'code',
        'jenis_biaya',
        'uraian',
        'kategori',
        'sub_kategori',
        'code_account',
        'total',
        'deskripsi'
    ];

    // Method untuk generate kode laporan berdasarkan minggu dan tahun
    public static function generateCode($minggu, $tahun)
    {
        return 'M' . $minggu . substr($tahun, -2); // Contoh format M624
    }
}

