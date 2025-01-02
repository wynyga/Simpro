<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{DayWork,Equipment,Tools,LandStoneSand,Cement,Rebar
                ,Wood,RoofCeilingTile,KeramikFloor,PaintGlassWallpaper
                ,Others,OilChemicalPerekat,Sanitary,PipingPump,Lighting,Log};

class Perumahan extends Model
{
    use HasFactory;
    protected $table = 'perumahan';

    protected $fillable = [
        'nama_perumahan',
        'lokasi'
    ];

        // Mendefinisikan relasi dengan tabel stock
    public function day_work()
    {
        return $this->hasMany(DayWork::class, 'perumahan_id');
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'perumahan_id');
    }

    public function tools()
    {
        return $this->hasMany(Tools::class, 'perumahan_id');
    }

    public function land_stone_sand()
    {
        return $this->hasMany(LandStoneSand::class, 'perumahan_id');
    }

    public function cement()
    {
        return $this->hasMany(Cement::class, 'perumahan_id');
    }

    public function rebar()
    {
        return $this->hasMany(Rebar::class, 'perumahan_id');
    }

    public function wood()
    {
        return $this->hasMany(Wood::class, 'perumahan_id');
    }

    public function roof_ceiling_tile()
    {
        return $this->hasMany(RoofCeilingTile::class, 'perumahan_id');
    }

    public function keramik_floor()
    {
        return $this->hasMany(KeramikFloor::class, 'perumahan_id');
    }

    public function paint_glass_wallpaper()
    {
        return $this->hasMany(PaintGlassWallpaper::class, 'perumahan_id');
    }

    public function other()
    {
        return $this->hasMany(Others::class, 'perumahan_id');
    }

    public function oil_chemical_perekat()
    {
        return $this->hasMany(OilChemicalPerekat::class, 'perumahan_id');
    }

    public function sanitary()
    {
        return $this->hasMany(Sanitary::class, 'perumahan_id');
    }

    public function piping_pump()
    {
        return $this->hasMany(PipingPump::class, 'perumahan_id');
    }

    public function lighting()
    {
        return $this->hasMany(Lighting::class, 'perumahan_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'perumahan_id');
    }

    // Relasi dengan tabel gudang_in
    public function gudangIns()
    {
        return $this->hasMany(GudangIn::class, 'perumahan_id');
    }

    // Relasi dengan tabel gudang_out
    public function gudangOuts()
    {
        return $this->hasMany(GudangOut::class, 'perumahan_id');
    }

    // Relasi dengan tabel transaksi_kas
    public function transaksiKas()
    {
        return $this->hasMany(TransaksiKas::class, 'perumahan_id');
    }

    // Relasi ke tipe_rumah
    public function tipeRumah()
    {
        return $this->hasMany(TipeRumah::class, 'id_perumahan');
    }
}
