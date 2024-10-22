@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Transaksi</h1>

    <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="id_blok_unit" class="form-label">Blok Unit</label>
            <select name="id_blok_unit" class="form-control" required>
                @foreach($blok_units as $blok_unit)
                    <option value="{{ $blok_unit->id }}" {{ $blok_unit->id == $transaksi->id_blok_unit ? 'selected' : '' }}>
                        {{ $blok_unit->blok }} - Unit {{ $blok_unit->unit }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="id_user" class="form-label">Nama User</label>
            <select name="id_user" class="form-control" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == $transaksi->id_user ? 'selected' : '' }}>
                        {{ $user->nama_user }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="harga_jual_standar" class="form-label">Harga Jual Standar</label>
            <input type="number" name="harga_jual_standar" class="form-control" value="{{ $transaksi->harga_jual_standar }}" required>
        </div>

        <div class="mb-3">
            <label for="kelebihan_tanah" class="form-label">Kelebihan Tanah</label>
            <input type="number" name="kelebihan_tanah" class="form-control" value="{{ $transaksi->kelebihan_tanah }}">
        </div>

        <div class="mb-3">
            <label for="penambahan_luas_bangunan" class="form-label">Penambahan Luas Bangunan</label>
            <input type="number" name="penambahan_luas_bangunan" class="form-control" value="{{ $transaksi->penambahan_luas_bangunan }}">
        </div>

        <div class="mb-3">
            <label for="perubahan_spek_bangunan" class="form-label">Perubahan Spek Bangunan</label>
            <input type="number" name="perubahan_spek_bangunan" class="form-control" value="{{ $transaksi->perubahan_spek_bangunan }}">
        </div>

        <div class="mb-3">
            <label for="total_harga_jual" class="form-label">Total Harga Jual</label>
            <input type="number" name="total_harga_jual" class="form-control" value="{{ $transaksi->total_harga_jual }}" required>
        </div>

        <div class="mb-3">
            <label for="kpr_disetujui" class="form-label">KPR Disetujui</label>
            <select name="kpr_disetujui" class="form-control" required>
                <option value="Ya" {{ $transaksi->kpr_disetujui == 'Ya' ? 'selected' : '' }}>Ya</option>
                <option value="Tidak" {{ $transaksi->kpr_disetujui == 'Tidak' ? 'selected' : '' }}>Tidak</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="minimum_dp" class="form-label">Minimum DP</label>
            <input type="number" name="minimum_dp" class="form-control" value="{{ $transaksi->minimum_dp }}" required>
        </div>

        <div class="mb-3">
            <label for="kewajiban_hutang" class="form-label">Kewajiban Hutang</label>
            <input type="number" name="kewajiban_hutang" class="form-control" value="{{ $transaksi->kewajiban_hutang }}">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

