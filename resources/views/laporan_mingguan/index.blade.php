<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan Mingguan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
        }
        .success-message {
            color: green;
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Daftar Laporan Mingguan</h1>

    @if (session('success'))
        <p class="success-message">{{ session('success') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Minggu Ke</th>
                <th>Tahun Ke</th>
                <th>Kode</th>
                <th>Uraian</th>
                <th>Jenis Biaya</th>
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th>Kode Akun</th>
                <th>Total (RP)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $item)
                <tr>
                    <td>{{ $item->minggu_ke }}</td>
                    <td>{{ $item->tahun_ke }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->uraian }}</td>
                    <td>{{ $item->jenis_biaya }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->sub_kategori }}</td>
                    <td>{{ $item->code_account }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($laporan->isEmpty())
        <p style="text-align: center;">Tidak ada data laporan mingguan yang tersedia.</p>
    @endif
</body>
</html>
