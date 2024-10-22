<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Transaksi</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav>
        <!-- Navigasi jika diperlukan -->
        <a href="{{ route('transaksi.index') }}">Daftar Transaksi</a>
    </nav>

    <div class="container">
        @yield('content')  <!-- Bagian ini akan digantikan oleh konten dari masing-masing view -->
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
