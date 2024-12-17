<?php

namespace App\Helpers;

use App\Models\{
    DayWork, Equipment, Tools, LandStoneSand, Cement, Rebar, Wood, RoofCeilingTile, KeramikFloor,
    PaintGlassWallpaper, Others, OilChemicalPerekat, Sanitary, PipingPump, Lighting
};

class StockHelper
{
    public static function getModelFromCode($kode)
    {
        $pos = strpos($kode, '-');
        $prefix = substr($kode, 0, $pos + 1); 

        switch ($prefix) {
            case 'MDW10-':
                return DayWork::where('kode', $kode)->first();
            case 'EQP20-':
                return Equipment::where('kode', $kode)->first();
            case 'EQT30-':
                return Tools::where('kode', $kode)->first();
            case 'LSS40-':
                return LandStoneSand::where('kode', $kode)->first();
            case 'CEM50-':
                return Cement::where('kode', $kode)->first();
            case 'REB30-':
                return Rebar::where('kode', $kode)->first();
            case 'WOD70-':
                return Wood::where('kode', $kode)->first();
            case 'RCT80-':
                return RoofCeilingTile::where('kode', $kode)->first();
            case 'KERM90-':
                return KeramikFloor::where('kode', $kode)->first();
            case 'PGW100-':
                return PaintGlassWallpaper::where('kode', $kode)->first();
            case 'OTH110-':
                return Others::where('kode', $kode)->first();
            case 'OCP110-':
                return OilChemicalPerekat::where('kode', $kode)->first();
            case 'SANI120-':
                return Sanitary::where('kode', $kode)->first();
            case 'PIPP130-':
                return PipingPump::where('kode', $kode)->first();
            case 'LIGH140-':
                return Lighting::where('kode', $kode)->first();
            default:
                return null;
        }
    }
}

