<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\{
    DayWork, Equipment, Tools, LandStoneSand, Cement, Rebar, Wood, RoofCeilingTile, KeramikFloor,
    PaintGlassWallpaper, Others, OilChemicalPerekat, Sanitary, PipingPump, Lighting
};
use App\Http\Resources\{
    DayWorkResource,EquipmentsResource,ToolsResource,LandStoneSandResource,CementResource,
    RebarResource,WoodResource,RoofCeilingTileResource,KeramikFloorResource,PaintGlassWallpaperResource,
    OthersResource,OilChemicalPerekatResource,SanitaryResource,PipingPumpResource,LightingResource
};

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $dayWorks = DayWorkResource::collection(DayWork::all());
        $equipments = EquipmentsResource::collection(Equipment::all());
        $tools = ToolsResource::collection(Tools::all());
        $landStoneSands = LandStoneSandResource::collection(LandStoneSand::all());
        $cements = CementResource::collection(Cement::all());
        $rebars = RebarResource::collection(Rebar::all());
        $woods = WoodResource::collection(Wood::all());
        $roofCeilingTiles = RoofCeilingTileResource::collection(RoofCeilingTile::all());
        $keramikFloors = KeramikFloorResource::collection(KeramikFloor::all());
        $paintGlassWallpapers = PaintGlassWallpaperResource::collection(PaintGlassWallpaper::all());
        $others = OthersResource::collection(Others::all());
        $oilChemicalPerekats = OilChemicalPerekatResource::collection(OilChemicalPerekat::all());
        $sanitaries = SanitaryResource::collection(Sanitary::all());
        $pipingPumps = PipingPumpResource::collection(PipingPump::all());
        $lightings = LightingResource::collection(Lighting::all());
        
        $data = [
            'day_works' => $dayWorks,
            'equipments' => $equipments,
            'tools' => $tools,
            'land_stone_sands' => $landStoneSands,
            'cements' => $cements,
            'rebars' => $rebars,
            'woods' => $woods,
            'roof_ceiling_tiles' => $roofCeilingTiles,
            'keramik_floors' => $keramikFloors,
            'paint_glass_wallpapers' => $paintGlassWallpapers,
            'others' => $others,
            'oil_chemical_perekats' => $oilChemicalPerekats,
            'sanitaries' => $sanitaries,
            'piping_pumps' => $pipingPumps,
            'lightings' => $lightings,
        ];
        

        // Mengembalikan semua data sebagai JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $data
        ]);

        // return view('stock');

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_peralatan' => 'required|string|in:day_work,equipment,tools,land_stone_sand,cement,rebar,wood,roof_ceiling_tile,keramik_floor,paint_glass_wallpaper,others,oil_chemical_perekat,sanitary,piping_pump,lighting',
            'nama_barang' => 'required',
            'uty' => 'required',
            'satuan' => 'required',
            'harga_satuan' => 'required|numeric',
            'stock_bahan' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $modelMap = [
                'day_work' => DayWork::class,
                'equipment' => Equipment::class,
                'tools' => Tools::class,
                'land_stone_sand' => LandStoneSand::class,
                'cement' => Cement::class,
                'rebar' => Rebar::class,
                'wood' => Wood::class,
                'roof_ceiling_tile' => RoofCeilingTile::class,
                'keramik_floor' => KeramikFloor::class,
                'paint_glass_wallpaper' => PaintGlassWallpaper::class,
                'others' => Others::class,
                'oil_chemical_perekat' => OilChemicalPerekat::class,
                'sanitary' => Sanitary::class,
                'piping_pump' => PipingPump::class,
                'lighting' => Lighting::class,
            ];
            

            $modelClass = $modelMap[$request->jenis_peralatan];
            $stock = new $modelClass;

            // Logic to generate code
            $prefix = $stock->getPrefix(); // Assume getPrefix method is defined in models
            $lastId = $modelClass::max('id') + 1;
            $stock->kode = $prefix . str_pad($lastId, 2, '0', STR_PAD_LEFT);

            // Fill stock data
            $stock->fill($request->only(['nama_barang', 'uty', 'satuan', 'harga_satuan', 'stock_bahan']));
            $stock->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data stock berhasil disimpan.',
                'data' => $stock
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

        return response()->json($codes);
    }
}

