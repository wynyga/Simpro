@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Tipe Rumah</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('tipe_rumah.create') }}" class="btn btn-primary mb-3">Tambah Tipe Rumah</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Perumahan</th>
                <th>Tipe Rumah</th>
                <th>Luas Bangunan (M2)</th>
                <th>Luas Kavling (M2)</th>
                <th>Harga Standar Tengah</th>
                <th>Harga Standar Sudut</th>
                <th>Penambahan Bangunan per M2</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tipe_rumahs as $tipe_rumah)
                <tr>
                    <td>{{ $tipe_rumah->id }}</td>
                    <td>{{ $tipe_rumah->perumahan->nama_perumahan }}</td>  <!-- Menampilkan nama perumahan -->
                    <td>{{ $tipe_rumah->tipe_rumah }}</td>
                    <td>{{ $tipe_rumah->luas_bangunan }}</td>
                    <td>{{ $tipe_rumah->luas_kavling }}</td>
                    <td>{{ number_format($tipe_rumah->harga_standar_tengah, 2) }}</td>
                    <td>{{ number_format($tipe_rumah->harga_standar_sudut, 2) }}</td>
                    <td>{{ number_format($tipe_rumah->penambahan_bangunan, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
