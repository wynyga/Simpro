<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Laporan Keuangan</title>
</head>
<body>
    <h1>Ringkasan Total per Jenis Biaya</h1>

    <!-- Cek apakah ada data ringkasan jenis biaya -->
    @if(count($summaryJenisBiaya) > 0)
        <table border="1">
            <thead>
                <tr>
                    <th>Jenis Biaya</th>
                    <th>Total Keuangan (RP)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($summaryJenisBiaya as $item)
                    <tr>
                        <td>{{ $item->jenis_biaya }}</td>
                        <td>{{ number_format($item->total_keuangan, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada data yang tersedia.</p>
    @endif

</body>
</html>
