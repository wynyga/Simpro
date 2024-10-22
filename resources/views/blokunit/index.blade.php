@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Blok Unit</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('blokunit.create') }}" class="btn btn-primary mb-3">Tambah Blok Unit</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Perumahan</th>
                <th>Tipe Rumah</th>
                <th>Blok</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($blok_units as $blok_unit)
                <tr>
                    <td>{{ $blok_unit->id }}</td>
                    <td>{{ $blok_unit->tipeRumah->perumahan->nama_perumahan }}</td> <!-- Menampilkan nama perumahan -->
                    <td>{{ $blok_unit->tipeRumah->tipe_rumah }}</td> <!-- Menampilkan tipe rumah -->
                    <td>{{ $blok_unit->blok }}</td>
                    <td>{{ $blok_unit->unit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
