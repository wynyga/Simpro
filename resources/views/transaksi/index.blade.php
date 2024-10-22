@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Transaksi</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('transaksi.create') }}" class="btn btn-primary mb-3">Tambah Transaksi</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Blok Unit</th>
                <th>User</th>
                <th>Total Harga Jual</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $trans)
                <tr>
                    <td>{{ $trans->id }}</td>
                    <td>{{ $trans->blokUnit->blok }} - Unit {{ $trans->blokUnit->unit }}</td>
                    <td>{{ $trans->userPerumahan->nama_user }}</td>
                    <td>{{ number_format($trans->total_harga_jual, 2) }}</td>
                    <td>
                        <a href="{{ route('transaksi.edit', $trans->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('transaksi.destroy', $trans->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
