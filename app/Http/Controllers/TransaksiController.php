<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Unit;
use App\Models\UserPerumahan;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
    
        $query = Transaksi::with('unit.blok', 'unit.tipeRumah', 'userPerumahan')
            ->where('perumahan_id', $user->perumahan_id);
    
        if ($search) {
            $query->whereHas('userPerumahan', function ($q) use ($search) {
                $q->where('nama_user', 'like', "%{$search}%");
            })->orWhereHas('unit', function ($q) use ($search) {
                $q->where('nomor_unit', 'like', "%{$search}%");
            });
        }
    
        return response()->json($query->paginate($perPage));
    }
    

    public function create()
    {
        $user = auth()->user();
        $unit = Unit::with('blok', 'tipeRumah')->whereHas('blok', function ($query) use ($user) {
            $query->where('perumahan_id', $user->perumahan_id);
        })->get();
        $users = UserPerumahan::where('perumahan_id', $user->perumahan_id)->get();
        return response()->json(['unit' => $unit, 'users' => $users]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'user_id' => 'required|exists:user_perumahan,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric|min:0',
            'biaya_booking' => 'nullable|numeric',
        ]);

        // Default nilai nullable
        $data['kelebihan_tanah'] = $data['kelebihan_tanah'] ?? 0;
        $data['penambahan_luas_bangunan'] = $data['penambahan_luas_bangunan'] ?? 0;
        $data['perubahan_spek_bangunan'] = $data['perubahan_spek_bangunan'] ?? 0;

        // Hitung total harga jual
        $data['total_harga_jual'] =
            $data['harga_jual_standar'] +
            $data['kelebihan_tanah'] +
            $data['penambahan_luas_bangunan'] +
            $data['perubahan_spek_bangunan'];

        // Validasi minimum DP
        if ($data['minimum_dp'] > $data['total_harga_jual']) {
            return response()->json(['error' => 'DP tidak boleh lebih besar dari total harga jual.'], 422);
        }

        // Cegah duplikasi penjualan untuk unit yang sama
        if (\App\Models\Transaksi::where('unit_id', $data['unit_id'])->exists()) {
            return response()->json(['error' => 'Unit ini sudah memiliki transaksi penjualan.'], 409);
        }

        // Tambahan metadata
        $data['plafon_kpr'] = $data['total_harga_jual'] - $data['minimum_dp'];
        $data['perumahan_id'] = $user->perumahan_id;
        $data['dibuat_oleh'] = $user->name;

        // Simpan transaksi penjualan
        $transaksi = \App\Models\Transaksi::create($data);

        // Jika ada DP, simpan ke transaksi kas dan buat kwitansi
        if ($data['minimum_dp'] > 0) {
            $kas = \App\Models\TransaksiKas::create([
                'tanggal' => now(),
                'kode' => '101', // Kas Masuk
                'jumlah' => $data['minimum_dp'],
                'saldo_setelah_transaksi' => null,
                'metode_pembayaran' => 'Cash',
                'dibuat_oleh' => $user->name,
                'keterangan_objek_transaksi' => 'DP Penjualan Unit: ' . $transaksi->unit->nomor_unit,
                'perumahan_id' => $user->perumahan_id,
                'status' => 'approved',
                'sumber_transaksi' => 'penjualan',
                'keterangan_transaksi_id' => $transaksi->id,
            ]);

            $no_doc = \App\Helpers\KwitansiService::generateNoDoc($kas->perumahan_id, 'CI');

            \App\Models\Kwitansi::create([
                'transaksi_kas_id' => $kas->id,
                'perumahan_id' => $kas->perumahan_id,
                'no_doc' => $no_doc,
                'tanggal' => now(),
                'dari' => $kas->dibuat_oleh,
                'jumlah' => $kas->jumlah,
                'untuk_pembayaran' => 'DP Penjualan Unit: ' . $transaksi->unit->nomor_unit,
                'metode_pembayaran' => $kas->metode_pembayaran,
                'dibuat_oleh' => $user->name,
                'disetor_oleh' => $kas->dibuat_oleh,
                'mengetahui' => null,
                'gudang_in_id' => null,
            ]);
        }

        return response()->json([
            'message' => 'Transaksi penjualan berhasil disimpan',
            'data' => $transaksi
        ], 201);
    }


    public function storeLama(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'user_id' => 'required|exists:user_perumahan,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric',
            'biaya_booking' => 'nullable|numeric',
        ]);
    
        $data['kelebihan_tanah'] = $data['kelebihan_tanah'] ?? 0;
        $data['penambahan_luas_bangunan'] = $data['penambahan_luas_bangunan'] ?? 0;
        $data['perubahan_spek_bangunan'] = $data['perubahan_spek_bangunan'] ?? 0;
    
        $data['total_harga_jual'] = 
            $data['harga_jual_standar'] + 
            $data['kelebihan_tanah'] + 
            $data['penambahan_luas_bangunan'] + 
            $data['perubahan_spek_bangunan'];
    
        $data['plafon_kpr'] = $data['total_harga_jual'] - $data['minimum_dp'];
        $data['perumahan_id'] = $user->perumahan_id;
    
        $transaksi = Transaksi::create($data);
    
        return response()->json(['message' => 'Transaksi berhasil ditambahkan', 'data' => $transaksi], 201);
    }
    
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $transaksi = \App\Models\Transaksi::where('id', $id)
            ->where('perumahan_id', $user->perumahan_id)
            ->firstOrFail();

        $validated = $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'user_id' => 'required|exists:user_perumahan,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric|min:0',
            'biaya_booking' => 'nullable|numeric',
        ]);

        // Nilai default
        $validated['kelebihan_tanah'] = $validated['kelebihan_tanah'] ?? 0;
        $validated['penambahan_luas_bangunan'] = $validated['penambahan_luas_bangunan'] ?? 0;
        $validated['perubahan_spek_bangunan'] = $validated['perubahan_spek_bangunan'] ?? 0;

        $validated['total_harga_jual'] =
            $validated['harga_jual_standar'] +
            $validated['kelebihan_tanah'] +
            $validated['penambahan_luas_bangunan'] +
            $validated['perubahan_spek_bangunan'];

        if ($validated['minimum_dp'] > $validated['total_harga_jual']) {
            return response()->json(['error' => 'DP tidak boleh melebihi total harga jual.'], 422);
        }

        $validated['plafon_kpr'] = $validated['total_harga_jual'] - $validated['minimum_dp'];

        // Perbarui transaksi penjualan
        $transaksi->update($validated);

        // Sync transaksi_kas untuk DP
        if ($validated['minimum_dp'] > 0) {
            $kas = \App\Models\TransaksiKas::where('sumber_transaksi', 'penjualan')
                ->where('keterangan_transaksi_id', $transaksi->id)
                ->first();

            if ($kas) {
                $kas->update([
                    'jumlah' => $validated['minimum_dp'],
                    'keterangan_objek_transaksi' => 'DP Penjualan Unit ID: ' . $transaksi->unit_id,
                ]);
            } else {
                $kas = \App\Models\TransaksiKas::create([
                    'tanggal' => now(),
                    'kode' => '101',
                    'jumlah' => $validated['minimum_dp'],
                    'saldo_setelah_transaksi' => null,
                    'metode_pembayaran' => 'Cash',
                    'dibuat_oleh' => $user->name,
                    'keterangan_objek_transaksi' => 'DP Penjualan Unit ID: ' . $transaksi->unit_id,
                    'perumahan_id' => $user->perumahan_id,
                    'status' => 'approved',
                    'sumber_transaksi' => 'penjualan',
                    'keterangan_transaksi_id' => $transaksi->id,
                ]);
            }

            // Sync atau buat kwitansi
            $kwitansi = \App\Models\Kwitansi::where('transaksi_kas_id', $kas->id)->first();
            $no_doc = \App\Helpers\KwitansiService::generateNoDoc($kas->perumahan_id, 'CI');

            if ($kwitansi) {
                $kwitansi->update([
                    'jumlah' => $kas->jumlah,
                    'untuk_pembayaran' => "DP Penjualan Unit ID: {$transaksi->unit_id}",
                ]);
            } else {
                \App\Models\Kwitansi::create([
                    'transaksi_kas_id' => $kas->id,
                    'perumahan_id' => $kas->perumahan_id,
                    'no_doc' => $no_doc,
                    'tanggal' => now(),
                    'dari' => $kas->dibuat_oleh,
                    'jumlah' => $kas->jumlah,
                    'untuk_pembayaran' => "DP Penjualan Unit ID: {$transaksi->unit_id}",
                    'metode_pembayaran' => $kas->metode_pembayaran,
                    'dibuat_oleh' => $user->name,
                    'disetor_oleh' => $kas->dibuat_oleh,
                    'mengetahui' => null,
                    'gudang_in_id' => null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Transaksi penjualan berhasil diperbarui',
            'data' => $transaksi
        ], 200);
    }
   
    public function destroy($id)
    {
        $user = auth()->user();
        $transaksi = Transaksi::where('id', $id)
                              ->where('perumahan_id', $user->perumahan_id)
                              ->firstOrFail();
        $transaksi->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus'], 204);
    }
}
