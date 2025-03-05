<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LapBulanan extends Model
{
    protected $table = 'lap_bulanan';

    protected $fillable = [
        'cost_structure_id',
        'bulan',
        'tahun',
        'jumlah',
        'status',
        'code_account'
    ];

    public function costStructure()
    {
        return $this->belongsTo(CostStructure::class, 'cost_structure_id');
    }

    // Generate Code Account Sebelum Menyimpan
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($laporan) {
            $kodeBulan = str_pad($laporan->bulan, 2, '0', STR_PAD_LEFT);
            $kodeTahun = substr($laporan->tahun, -2);
            $laporan->code_account = $laporan->costStructure->cost_tee_code . 'B' . $kodeBulan . $kodeTahun;
        });
    }
}

