@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Tipe Rumah</h1>

    <form action="{{ route('tipe_rumah.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="id_perumahan" class="form-label">Nama Perumahan</label>
            <select name="id_perumahan" class="form-control" required>
                <option value="">-- Pilih Perumahan --</option>
                @foreach($perumahans as $perumahan)
                    <option value="{{ $perumahan->id }}">{{ $perumahan->nama_perumahan }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tipe_rumah" class="form-label">Tipe Rumah</label>
            <input type="text" name="tipe_rumah" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="luas_bangunan" class="form-label">Luas Bangunan (M2)</label>
            <input type="number" name="luas_bangunan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="luas_kavling" class="form-label">Luas Kavling (M2)</label>
            <input type="number" name="luas_kavling" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="harga_standar_tengah" class="form-label">Harga Standar Tengah</label>
            <input type="number" name="harga_standar_tengah" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="harga_standar_sudut" class="form-label">Harga Standar Sudut</label>
            <input type="number" name="harga_standar_sudut" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="penambahan_bangunan" class="form-label">Penambahan Bangunan per M2</label>
            <input type="number" name="penambahan_bangunan" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('tipe_rumah.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
