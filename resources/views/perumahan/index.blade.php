@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Perumahan</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('perumahan.create') }}" class="btn btn-primary mb-3">Tambah Perumahan</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Perumahan</th>
                <th>Lokasi</th>
                <th>Tanggal Update Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perumahans as $perumahan)
                <tr>
                    <td>{{ $perumahan->id }}</td>
                    <td>{{ $perumahan->nama_perumahan }}</td>
                    <td>{{ $perumahan->lokasi }}</td>
                    <td>{{ $perumahan->tanggal_harga }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
