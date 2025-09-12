<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Unit;
use App\Models\UserPerumahan;
use App\Models\TransaksiKas;
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

        // Validasi transaksi
        $data = $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric|min:0',
            'biaya_booking' => 'nullable|numeric',

            // ✅ Validasi data user baru
            'nama_user' => 'required|string|max:255',
            'alamat_user' => 'required|string|max:255',
            'no_telepon' => 'required|digits_between:10,15',
        ]);

        // Buat user baru di tabel UserPerumahan
        $userPerumahan = \App\Models\UserPerumahan::create([
            'nama_user' => $data['nama_user'],
            'alamat_user' => $data['alamat_user'],
            'no_telepon' => $data['no_telepon'],
            'perumahan_id' => $user->perumahan_id,
        ]);

        // Masukkan user_id ke data transaksi
        $data['user_id'] = $userPerumahan->id;

        // Default nilai nullable
        $data['kelebihan_tanah'] = $data['kelebihan_tanah'] ?? 0;
        $data['penambahan_luas_bangunan'] = $data['penambahan_luas_bangunan'] ?? 0;
        $data['perubahan_spek_bangunan'] = $data['perubahan_spek_bangunan'] ?? 0;
        $data['biaya_booking'] = $data['biaya_booking'] ?? 0;

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

        // Cegah duplikasi penjualan unit
        if (\App\Models\Transaksi::where('unit_id', $data['unit_id'])->exists()) {
            return response()->json(['error' => 'Unit ini sudah memiliki transaksi penjualan.'], 409);
        }

        // Hitung sisa hutang
        $data['sisa_hutang'] = $data['total_harga_jual'] - $data['minimum_dp'];

        // Metadata tambahan
        $data['perumahan_id'] = $user->perumahan_id;
        $data['dibuat_oleh'] = $user->name;

        // Simpan transaksi
        $transaksi = \App\Models\Transaksi::create($data);

        // Jika ada DP, catat kas & kwitansi
        if ($data['minimum_dp'] > 0) {
            $kas = \App\Models\TransaksiKas::create([
                'tanggal' => now(),
                'kode' => '101',
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
            'message' => 'Transaksi dan user baru berhasil disimpan',
            'data' => [
                'transaksi' => $transaksi,
                'user' => $userPerumahan,
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $transaksi = \App\Models\Transaksi::where('id', $id)
            ->where('perumahan_id', $user->perumahan_id)
            ->firstOrFail();

        $validated = $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric|min:0',
            'biaya_booking' => 'nullable|numeric',

            // ✅ Tambahkan validasi user
            'user_id' => 'nullable|exists:user_perumahan,id',
            'nama_user' => 'nullable|string|max:255',
            'alamat_user' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|digits_between:10,15',
        ]);

        /**
         * Jika `user_id` dikirim → pakai user itu.
         * Jika tidak, tapi ada data user → buat user baru.
         */
        if (!empty($validated['user_id'])) {
            $validated['user_id'] = $validated['user_id'];
        } elseif (!empty($validated['nama_user']) && !empty($validated['alamat_user']) && !empty($validated['no_telepon'])) {
            $userPerumahan = \App\Models\UserPerumahan::create([
                'nama_user' => $validated['nama_user'],
                'alamat_user' => $validated['alamat_user'],
                'no_telepon' => $validated['no_telepon'],
                'perumahan_id' => $user->perumahan_id,
            ]);
            $validated['user_id'] = $userPerumahan->id;
        } else {
            return response()->json(['error' => 'User harus dipilih atau data user baru harus lengkap.'], 422);
        }

        // Nilai default
        $validated['kelebihan_tanah'] = $validated['kelebihan_tanah'] ?? 0;
        $validated['penambahan_luas_bangunan'] = $validated['penambahan_luas_bangunan'] ?? 0;
        $validated['perubahan_spek_bangunan'] = $validated['perubahan_spek_bangunan'] ?? 0;
        $validated['biaya_booking'] = $validated['biaya_booking'] ?? 0;

        // Hitung total harga jual
        $validated['total_harga_jual'] =
            $validated['harga_jual_standar'] +
            $validated['kelebihan_tanah'] +
            $validated['penambahan_luas_bangunan'] +
            $validated['perubahan_spek_bangunan'];

        if ($validated['minimum_dp'] > $validated['total_harga_jual']) {
            return response()->json(['error' => 'DP tidak boleh melebihi total harga jual.'], 422);
        }

        // Hitung sisa hutang
        $validated['sisa_hutang'] = $validated['total_harga_jual'] - $validated['minimum_dp'];

        // Update transaksi
        $transaksi->update($validated);

        // Sinkronisasi DP ke TransaksiKas + Kwitansi
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

            // Kwitansi
            $kwitansi = \App\Models\Kwitansi::where('transaksi_kas_id', $kas->id)->first();
            $no_doc = \App\Helpers\KwitansiService::generateNoDoc($kas->perumahan_id, 'CI');

            if ($kwitansi) {
                $kwitansi->update([
                    'jumlah' => $kas->jumlah,
                    'untuk_pembayaran' => "DP Penjualan Unit ID: {$transaksi->unit_id}",
                    'metode_pembayaran' => $kas->metode_pembayaran,
                    'tanggal' => now(),
                    'dari' => $kas->dibuat_oleh,
                    'disetor_oleh' => $kas->dibuat_oleh,
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

        $transaksi = \App\Models\Transaksi::where('id', $id)
            ->where('perumahan_id', $user->perumahan_id)
            ->firstOrFail();

        $userId = $transaksi->user_id;

        // Cari transaksi kas terkait
        $kas = \App\Models\TransaksiKas::where('sumber_transaksi', 'penjualan')
            ->where('keterangan_transaksi_id', $transaksi->id)
            ->first();

        if ($kas) {
            // Hapus kwitansi yang terkait transaksi kas ini
            \App\Models\Kwitansi::where('transaksi_kas_id', $kas->id)->delete();

            // Hapus transaksi kas
            $kas->delete();
        }

        // Hapus transaksi
        $transaksi->delete();

        // Cek apakah user masih dipakai di transaksi lain
        $isUsed = \App\Models\Transaksi::where('user_id', $userId)->exists();

        if (!$isUsed) {
            \App\Models\UserPerumahan::where('id', $userId)->delete();
        }

        return response()->json([
            'message' => 'Transaksi, transaksi kas, kwitansi, dan user terkait (jika tidak digunakan lagi) berhasil dihapus'
        ], 200);
    }

    public function listAll()
    {
        $user = auth()->user();

        $transaksi = Transaksi::with('unit', 'userPerumahan')
            ->where('perumahan_id', $user->perumahan_id)
            ->get();

        return response()->json($transaksi);
    }

}
