<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Laporan Mingguan</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Ringkasan Total per Jenis Biaya, Uraian, dan Kategori</h1>

    <!-- Tampilkan ringkasan total per jenis biaya -->
    <h2>A. KAS PROJECT / KAS MASUK MINGGU INI</h2>
    <table>
        <tr>
            <th>Uraian</th>
            <th>Kategori</th>
            <th>Kode Akun</th>
            <th>Total Keuangan (RP)</th>
        </tr>
        @foreach ($summaryUraian as $uraian)
            @if ($uraian->jenis_biaya == 'KAS PROJECT / KAS MASUK MINGGU INI')
                <tr>
                    <td>{{ $uraian->uraian }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($uraian->total_keuangan, 2) }}</td>
                </tr>

                @foreach ($summaryKategori as $kategori)
                    @if ($kategori->uraian == $uraian->uraian)
                        <tr>
                            <td></td>
                            <td>{{ $kategori->kategori }}</td>
                            <td>{{ $kategori->code_account }}</td>
                            <td>{{ number_format($kategori->total_keuangan, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
        @endforeach
    </table>

    <!-- Ulangi struktur di atas untuk jenis biaya lain (Kas Keluar, Sisa Kas, Hutang Material, dll.) -->
    
    <h2>B. KAS KELUAR MINGGU INI</h2>
    <table>
        <tr>
            <th>Uraian</th>
            <th>Kategori</th>
            <th>Kode Akun</th>
            <th>Total Keuangan (RP)</th>
        </tr>
        @foreach ($summaryUraian as $uraian)
            @if ($uraian->jenis_biaya == 'KAS KELUAR MINGGU INI')
                <tr>
                    <td>{{ $uraian->uraian }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($uraian->total_keuangan, 2) }}</td>
                </tr>

                @foreach ($summaryKategori as $kategori)
                    @if ($kategori->uraian == $uraian->uraian)
                        <tr>
                            <td></td>
                            <td>{{ $kategori->kategori }}</td>
                            <td>{{ $kategori->code_account }}</td>
                            <td>{{ number_format($kategori->total_keuangan, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
        @endforeach
    </table>

    <!-- Tambahkan bagian lain untuk jenis biaya lainnya seperti SISA KAS PROJECT MINGGU INI, dll. -->

</body>
</html>
