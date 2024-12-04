<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DayWork; // Contoh model, sesuaikan dengan stock
use App\Models\Equipment;
use App\Models\Tools;
use App\Models\LandStoneSand;
use App\Models\Cement;
use App\Models\Rebar;
use App\Models\Wood;
use App\Models\RoofCeilingTile;
use App\Models\KeramikFloor;
use App\Models\PaintGlassWallpaper;
use App\Models\Others;
use App\Models\OilChemicalPerekat;
use App\Models\Sanitary;
use App\Models\PipingPump;
use App\Models\Lighting;

class StockController extends Controller
{
    public function index()
    {
        return view('stock');
    }

    public function store(Request $request)
    {
        // Tentukan jenis peralatan dari input form
        $jenisPeralatan = $request->input('jenis_peralatan');
        $prefix = '';
    
        // Buat instance model yang sesuai dengan jenis peralatan
        switch ($jenisPeralatan) {
            case 'day_work':
                $stock = new DayWork();
                $prefix = 'MDW10-';
                break;
            case 'equipment':
                $stock = new Equipment();
                $prefix = 'EQP20-';
                break;
            case 'tools':
                $stock = new Tools();
                $prefix = 'EQT30-';
                break;
            case 'land_stone_sand':
                $stock = new LandStoneSand();
                $prefix = 'LSS40-';
                break;
            case 'cement':
                $stock = new Cement();
                $prefix = 'CEM50-';
                break;
            case 'rebar':
                $stock = new Rebar();
                $prefix = 'REB30-';
                break;
            case 'wood':
                $stock = new Wood();
                $prefix = 'WOD70-';
                break;
            case 'roof_ceiling_tile':
                $stock = new RoofCeilingTile();
                $prefix = 'RCT80-';
                break;
            case 'keramik_floor':
                $stock = new KeramikFloor();
                $prefix = 'KERM90-';
                break;
            case 'paint_glass_wallpaper':
                $stock = new PaintGlassWallpaper();
                $prefix = 'PGW100-';
                break;
            case 'others':
                $stock = new Others();
                $prefix = 'OTH110-';
                break;
            case 'oil_chemical_perekat':
                $stock = new OilChemicalPerekat();
                $prefix = 'OCP110-';
                break;
            case 'sanitary':
                $stock = new Sanitary();
                $prefix = 'SANI120-';
                break;
            case 'piping_pump':
                $stock = new PipingPump();
                $prefix = 'PIPP130-';
                break;
            case 'lighting':
                $stock = new Lighting();
                $prefix = 'LIGH140-';
                break;
            default:
                return redirect('/stock')->with('error', 'Jenis peralatan tidak valid.');
        }
    
        // Generate kode barang otomatis (misalnya berdasarkan ID terakhir)
        $lastStock = $stock->orderBy('id', 'desc')->first();
        $newId = $lastStock ? $lastStock->id + 1 : 1;
        $stock->kode = $prefix . str_pad($newId, 2, '0', STR_PAD_LEFT);
    
        // Isi data dari form
        $stock->nama_barang = $request->nama_barang;
        $stock->uty = $request->uty;
        $stock->satuan = $request->satuan;
        $stock->harga_satuan = $request->harga_satuan;
        $stock->stock_bahan = $request->stock_bahan;
    
        // Simpan data
        $stock->save();

        //Uji melalui HTML
        //return redirect('/stock')->with('success', 'Data stock berhasil disimpan.');

        //Uji melalui Postman
        return response()->json([
            'status' => 'success',
            'message' => 'Data stock berhasil disimpan.',
            'data' => $stock
        ], 201);
    }
    

    public function getStockCodes($type)
    {
        $codes = [];

        switch ($type) {
            case 'day_work':
                $codes = DayWork::select('kode', 'nama_barang')->get();
                break;
            case 'equipment':
                $codes = Equipment::select('kode', 'nama_barang')->get();
                break;
            case 'tools':
                $codes = Tools::select('kode', 'nama_barang')->get();
                break;
            case 'land_stone_sand':
                $codes = LandStoneSand::select('kode', 'nama_barang')->get();
                break;
            case 'cement':
                $codes = Cement::select('kode', 'nama_barang')->get();
                break;
            case 'rebar':
                $codes = Rebar::select('kode', 'nama_barang')->get();
                break;
            case 'wood':
                $codes = Wood::select('kode', 'nama_barang')->get();
                break;
            case 'roof_ceiling_tile':
                $codes = RoofCeilingTile::select('kode', 'nama_barang')->get();
                break;
            case 'keramik_floor':
                $codes = KeramikFloor::select('kode', 'nama_barang')->get();
                break;
            case 'paint_glass_wallpaper':
                $codes = PaintGlassWallpaper::select('kode', 'nama_barang')->get();
                break;
            case 'others':
                $codes = Others::select('kode', 'nama_barang')->get();
                break;
            case 'oil_chemical_perekat':
                $codes = OilChemicalPerekat::select('kode', 'nama_barang')->get();
                break;
            case 'sanitary':
                $codes = Sanitary::select('kode', 'nama_barang')->get();
                break;
            case 'piping_pump':
                $codes = PipingPump::select('kode', 'nama_barang')->get();
                break;
            case 'lighting':
                $codes = Lighting::select('kode', 'nama_barang')->get();
                break;
            default:
                $codes = [];
                break;
        }

        //return response()->json($codes);
        return response()->json([
            'status' => 'success',
            'data' => $codes
        ]);
    }
}

