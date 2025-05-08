<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaintGlassWallpaper extends Model
{
    protected $table = 'paint_glass_wallpaper';
    protected $fillable = [
        'nama_barang',
        'uty',
        'satuan',
        'harga_satuan',
        'stock_bahan',
        'perumahan_id'
    ];
    public function getPrefix()
    {
        return 'PGW100-';
    }

    // Relasi balik ke Perumahan
    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

}
