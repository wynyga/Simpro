<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanBulanan extends Model
{
    use HasFactory;

    protected $table = 'laporan_bulanan'; // Memastikan model terhubung ke tabel yang benar

    protected $fillable = [
        'bulan_ke',
        'tahun_ke',
        'code',
        'jenis_biaya',
        'uraian',
        'kategori',
        'sub_kategori',
        'code_account',
        'sub_subkategori',
        'total',
        'deskripsi'
    ];

    // Casts untuk memastikan data masuk dan keluar dari database dengan format yang benar
    protected $casts = [
        'bulan_ke' => 'integer',
        'tahun_ke' => 'integer',
        'total' => 'decimal:2',
    ];
}
