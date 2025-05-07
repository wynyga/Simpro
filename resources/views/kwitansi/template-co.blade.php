<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi CO - {{ $kwitansi->no_doc }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 4px;
            vertical-align: top;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
            table-layout: fixed;
        }
        .signature-table td {
            width: 33%;
            word-wrap: break-word;
            padding: 0 10px;
        }
        .small {
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/BumiAsih.png') }}" alt="Logo">
        <h2>BUKTI PEMBAYARAN / PENGELUARAN KAS/BANK*</h2>
        <p>No. Dokumen: {{ $kwitansi->no_doc }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($kwitansi->tanggal)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="section">
        <table>
            <tr>
                <td width="30%">Sudah Terima Dari</td>
                <td>: {{ $kwitansi->dari }}</td>
            </tr>
            <tr>
                <td>Uang Sejumlah</td>
                <td>: Rp {{ number_format($kwitansi->jumlah, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Untuk Pembayaran</td>
                <td>: {{ $kwitansi->untuk_pembayaran }}</td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>: {{ $kwitansi->metode_pembayaran ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="signature-table">
        <tr>
            <td >Mengetahui,</td>
            <td >Dibuat Oleh,</td>
            <td >Disetor Oleh,</td>
        </tr>
        <tr style="height: 60px;">
            <td style="padding-top: 40px;">( ......................... )</td>
            <td style="padding-top: 40px;">( ......................... )</td>
            <td style="padding-top: 40px;">( ......................... )</td>
        </tr>
        <tr>
            <td class="small" style="margin-top: 10px;">Project Manager</td>
            <td class="small" style="margin-top: 10px;">Cashier</td>
            <td class="small" style="margin-top: 10px;">Customer</td>
        </tr>
    </table>

</body>
</html>
