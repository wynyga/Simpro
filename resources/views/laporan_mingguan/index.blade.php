<!DOCTYPE html>
<html>
<head>
    <title>Daftar Laporan Mingguan</title>
</head>
<body>
    <h1>Daftar Laporan Mingguan</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table border="1">
        <thead>
            <tr>
                <th>Minggu Ke</th>
                <th>Tahun Ke</th>
                <th>Kode</th>
                <th>Uraian</th>
                <th>Code Account</th>
                <th>Total (RP)</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $item)
                <tr>
                    <td>{{ $item->minggu_ke }}</td>
                    <td>{{ $item->tahun_ke }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->uraian }}</td>
                    <td>{{ $item->code_account }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                    <td>{{ $item->kategori }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
