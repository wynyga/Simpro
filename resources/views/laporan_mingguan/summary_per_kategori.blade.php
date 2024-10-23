<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Total per Kategori</title>
</head>
<body>
    <h1>Ringkasan Total per Kategori</h1>

    @if(count($summaryKategori) > 0)
        <table border="1">
            <thead>
                <tr>
                    <th>Jenis Biaya</th>
                    <th>Uraian</th>
                    <th>Kategori</th>
                    <th>Total Keuangan (RP)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($summaryKategori as $item)
                    <tr>
                        <td>{{ $item->jenis_biaya }}</td>
                        <td>{{ $item->uraian }}</td>
                        <td>{{ $item->kategori }}</td>
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
