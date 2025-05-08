<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DayWork;
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
use App\Models\Log;

class GudangOut extends Model
{
    protected $table = 'gudang_out';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'tanggal',
        'peruntukan',
        'status',
        'jumlah',
        'satuan',
        'jumlah_harga',
        'keterangan',
        'perumahan_id'
    ];
}
