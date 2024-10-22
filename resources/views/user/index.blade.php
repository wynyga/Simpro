@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar User</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('user.create') }}" class="btn btn-primary mb-3">Tambah User</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama User</th>
                <th>Alamat</th>
                <th>No Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->nama_user }}</td>
                    <td>{{ $user->alamat_user }}</td>
                    <td>{{ $user->no_telepon }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
