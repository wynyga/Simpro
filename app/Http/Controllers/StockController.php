<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\{
    DayWork, Equipment, Tools, LandStoneSand, Cement, Rebar, Wood, RoofCeilingTile, KeramikFloor,
    PaintGlassWallpaper, Others, OilChemicalPerekat, Sanitary, PipingPump, Lighting
};
use App\Helpers\StockHelper;

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
    
        $models = [
            'day_works' => DayWork::class,
            'equipments' => Equipment::class,
            'tools' => Tools::class,
            'land_stone_sands' => LandStoneSand::class,
            'cements' => Cement::class,
            'rebars' => Rebar::class,
            'woods' => Wood::class,
            'roof_ceiling_tiles' => RoofCeilingTile::class,
            'keramik_floors' => KeramikFloor::class,
            'paint_glass_wallpapers' => PaintGlassWallpaper::class,
            'others' => Others::class,
            'oil_chemical_perekats' => OilChemicalPerekat::class,
            'sanitaries' => Sanitary::class,
            'piping_pumps' => PipingPump::class,
            'lightings' => Lighting::class
        ];
    
        $data = [];
    
        foreach ($models as $key => $model) {
            $data[$key] = $model::where('perumahan_id', $perumahanId)->get()->map(function ($item) {
                return [
                    'type' => $item->kode ?? null,
                    'nama_barang' => $item->nama_barang,
                    'uty' => $item->uty,
                    'harga_satuan' => number_format($item->harga_satuan, 2, ',', '.'),
                    'stock_bahan' => $item->stock_bahan,
                    'total_price' => number_format($item->harga_satuan * $item->stock_bahan, 2, ',', '.') // Perbaikan disini
                ];
            });
        }
    
        // Mengembalikan semua data sebagai JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $data
        ]);
    }
    

    public function store(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
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
    
            // Ambil prefix dari model
            $prefix = $stock->getPrefix(); // asumsi setiap model punya method getPrefix()
    
            // Ambil stok terakhir berdasarkan perumahan_id
            $lastForPerumahan = $modelClass::where('perumahan_id', $perumahanId)
                ->orderByDesc('id')
                ->first();
    
            $nextNumber = 1;
    
            if ($lastForPerumahan && preg_match('/\-(\d{2})$/', $lastForPerumahan->kode, $match)) {
                $nextNumber = (int)$match[1] + 1;
            }
    
            $stock->kode = $prefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    
            // Simpan data
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
    
    public function update(Request $request, $kode_barang)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'perumahan_id is required'], 403);
        }
    
        // Menggunakan StockHelper untuk mendapatkan model berdasarkan kode_barang
        $stock = StockHelper::getModelFromCode($kode_barang, $perumahanId);
    
        if (!$stock) {
            return response()->json(['error' => 'Stock not found'], 404);
        }
    
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string',
            'uty' => 'required|string',
            'satuan' => 'required|string',
            'harga_satuan' => 'required|numeric|min:0',
            'stock_bahan' => 'required|numeric|min:0'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Update data stock berdasarkan input
        $stock->update($request->only(['nama_barang', 'uty', 'satuan', 'harga_satuan', 'stock_bahan']));
    
        return response()->json([
            'message' => 'Stock berhasil diperbarui',
            'data' => $stock
        ], 200);
    }
    

    public function destroy($id)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
        $jenis_peralatan = request()->input('jenis_peralatan');  // Assume jenis_peralatan is passed in the request

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

        if (!array_key_exists($jenis_peralatan, $modelMap)) {
            return response()->json(['error' => 'Jenis peralatan tidak valid'], 400);
        }

        $modelClass = $modelMap[$jenis_peralatan];
        $stock = $modelClass::where('id', $id)->where('perumahan_id', $perumahanId)->first();

        if (!$stock) {
            return response()->json(['error' => 'Stock not found'], 404);
        }

        $stock->delete();
        return response()->json(['message' => 'Stock berhasil dihapus'], 204);
    }

    public function getStockInventory()
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $totalStockValue = 0;

        $models = [
            DayWork::class, Equipment::class, Tools::class, LandStoneSand::class, Cement::class,
            Rebar::class, Wood::class, RoofCeilingTile::class, KeramikFloor::class, PaintGlassWallpaper::class,
            Others::class, OilChemicalPerekat::class, Sanitary::class, PipingPump::class, Lighting::class
        ];

        foreach ($models as $model) {
            $totalStockValue += $model::where('perumahan_id', $perumahanId)
                ->sum(DB::raw('harga_satuan * stock_bahan'));
        }

        return response()->json([
            'persediaan_bahan' => [
                'code_account' => 'GD0102',
                'total_rp' => round($totalStockValue, 2)
            ]
        ]);
    }

    
    
    public function searchStock(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        // Ambil parameter pencarian dari request
        $nama_barang = $request->query('nama_barang');
        $kode_barang = $request->query('kode_barang');
    
        $models = [
            'day_works' => DayWork::class,
            'equipments' => Equipment::class,
            'tools' => Tools::class,
            'land_stone_sands' => LandStoneSand::class,
            'cements' => Cement::class,
            'rebars' => Rebar::class,
            'woods' => Wood::class,
            'roof_ceiling_tiles' => RoofCeilingTile::class,
            'keramik_floors' => KeramikFloor::class,
            'paint_glass_wallpapers' => PaintGlassWallpaper::class,
            'others' => Others::class,
            'oil_chemical_perekats' => OilChemicalPerekat::class,
            'sanitaries' => Sanitary::class,
            'piping_pumps' => PipingPump::class,
            'lightings' => Lighting::class
        ];
    
        $results = [];
    
        foreach ($models as $key => $model) {
            $query = $model::where('perumahan_id', $perumahanId);
    
            if (!empty($nama_barang)) {
                $query->where('nama_barang', 'LIKE', "%$nama_barang%");
            }
    
            if (!empty($kode_barang)) {
                $query->where('kode', 'LIKE', "%$kode_barang%");
            }
    
            $items = $query->get();
    
            if ($items->isNotEmpty()) {
                $results[$key] = $items->map(function ($item) {
                    return [
                        'kode_barang' => $item->kode,
                        'nama_barang' => $item->nama_barang,
                        'uty' => $item->uty,
                        'satuan' => $item->satuan,
                        'harga_satuan' => number_format($item->harga_satuan, 2, ',', '.'),
                        'stock_bahan' => $item->stock_bahan,
                        'total_price' => number_format($item->harga_satuan * $item->stock_bahan, 2, ',', '.')
                    ];
                });
            }
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $results
        ]);
    }
    
}

