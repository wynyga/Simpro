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
        'jumlah'
    ];

    public function costStructure()
    {
        return $this->belongsTo(CostStructure::class, 'cost_structure_id');
    }

    // Generate Code Account Otomatis
    public function getCodeAccountAttribute()
    {
        $kodeBulan = str_pad($this->bulan, 2, '0', STR_PAD_LEFT);
        $kodeTahun = substr($this->tahun, -2);
        return $this->costStructure->costTee->cost_tee_code . 'B' . $kodeBulan . $kodeTahun;
    }
}

