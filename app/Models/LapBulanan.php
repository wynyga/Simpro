<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LapBulanan extends Model
{
    protected $table = 'lap_bulanan';

    protected $fillable = [
        'cost_tee_id',
        'bulan',
        'tahun',
        'jumlah',
        'status',
        'perumahan_id',
        'code_account'
    ];

    protected $casts = [
        'jumlah' => 'float'
    ];

    public function costTee()
    {
        return $this->belongsTo(CostTee::class, 'cost_tee_id');
    }

    public function costCentre()
    {
        return $this->hasOneThrough(
            CostCentre::class,
            CostElement::class,
            'id', // foreign key di cost_elements
            'id', // foreign key di cost_centres
            'costTee.cost_element_id', // foreign key di lap_bulanan
            'cost_centre_id' // foreign key di cost_elements
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($laporan) {
            $kodeBulan = str_pad($laporan->bulan, 2, '0', STR_PAD_LEFT);
            $kodeTahun = substr($laporan->tahun, -2);

            if ($laporan->costTee) {
                $laporan->code_account = $laporan->costTee->cost_tee_code . 'B' . $kodeBulan . $kodeTahun;
            }
        });
    }
}
