@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah User</h1>

    <form action="{{ route('user.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama_user" class="form-label">Nama User</label>
            <input type="text" name="nama_user" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="alamat_user" class="form-label">Alamat</label>
            <input type="text" name="alamat_user" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="no_telepon" class="form-label">No Telepon</label>
            <input type="text" name="no_telepon" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('user.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
