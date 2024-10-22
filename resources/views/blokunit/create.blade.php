@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Blok Unit</h1>

    <form action="{{ route('blokunit.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="blok" class="form-label">Blok</label>
            <input type="text" name="blok" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="unit" class="form-label">Unit</label>
            <input type="text" name="unit" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="id_tipe_rumah" class="form-label">Tipe Rumah</label>
            <select name="id_tipe_rumah" class="form-control" required>
                @foreach($tipe_rumah as $tipe)
                    <option value="{{ $tipe->id }}">{{ $tipe->tipe_rumah }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('blokunit.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
