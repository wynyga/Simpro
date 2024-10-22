@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Perumahan</h1>

    <form action="{{ route('perumahan.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama_perumahan" class="form-label">Nama Perumahan</label>
            <input type="text" name="nama_perumahan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi</label>
            <input type="text" name="lokasi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tanggal_harga" class="form-label">Tanggal Update Harga</label>
            <input type="date" name="tanggal_harga" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('perumahan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
