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
        $user = auth()->user(); // Mendapatkan user yang sedang login
        $perumahanId = $user->perumahan_id; // Mendapatkan perumahan_id dari user
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $dayWorks = DayWorkResource::collection(DayWork::where('perumahan_id', $perumahanId)->get());
        $equipments = EquipmentsResource::collection(Equipment::where('perumahan_id', $perumahanId)->get());
        $tools = ToolsResource::collection(Tools::where('perumahan_id', $perumahanId)->get());
        $landStoneSands = LandStoneSandResource::collection(LandStoneSand::where('perumahan_id', $perumahanId)->get());
        $cements = CementResource::collection(Cement::where('perumahan_id', $perumahanId)->get());
        $rebars = RebarResource::collection(Rebar::where('perumahan_id', $perumahanId)->get());
        $woods = WoodResource::collection(Wood::where('perumahan_id', $perumahanId)->get());
        $roofCeilingTiles = RoofCeilingTileResource::collection(RoofCeilingTile::where('perumahan_id', $perumahanId)->get());
        $keramikFloors = KeramikFloorResource::collection(KeramikFloor::where('perumahan_id', $perumahanId)->get());
        $paintGlassWallpapers = PaintGlassWallpaperResource::collection(PaintGlassWallpaper::where('perumahan_id', $perumahanId)->get());
        $others = OthersResource::collection(Others::where('perumahan_id', $perumahanId)->get());
        $oilChemicalPerekats = OilChemicalPerekatResource::collection(OilChemicalPerekat::where('perumahan_id', $perumahanId)->get());
        $sanitaries = SanitaryResource::collection(Sanitary::where('perumahan_id', $perumahanId)->get());
        $pipingPumps = PipingPumpResource::collection(PipingPump::where('perumahan_id', $perumahanId)->get());
        $lightings = LightingResource::collection(Lighting::where('perumahan_id', $perumahanId)->get());
        
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
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
        if (empty($perumahanId)) {
            // Log atau handle kasus ketika perumahan_id tidak ditemukan
            return response()->json(['error' => 'perumahan_id is required'], 403);
        }

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
            if (!array_key_exists($request->jenis_peralatan, $modelMap)) {
                return response()->json(['error' => 'Jenis peralatan tidak valid'], 400);
            }
            

            $modelClass = $modelMap[$request->jenis_peralatan];
            $stock = new $modelClass;

            // Logic to generate code
            $prefix = $stock->getPrefix(); // Assume getPrefix method is defined in models
            $lastId = $modelClass::max('id') + 1;
            $stock->kode = $prefix . str_pad($lastId, 2, '0', STR_PAD_LEFT);

            // Fill stock data
            $stock->fill($request->only(['nama_barang', 'uty', 'satuan', 'harga_satuan', 'stock_bahan']));
            $stock->perumahan_id = $perumahanId; 
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

