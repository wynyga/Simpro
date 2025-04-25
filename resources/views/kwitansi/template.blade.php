<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - {{ $kwitansi->no_doc }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }
        .signature-table td {
            padding: 0 10px;
        }
        .bold {
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .small {
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>KWITANSI</h2>
        <p>No. Doc: {{ $kwitansi->no_doc }}</p>
    </div>

    <div class="section">
        <table>
            <tr>
                <td width="30%">Sudah terima dari</td>
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
                <td>Jenis Penerimaan</td>
                <td>: {{ $kwitansi->jenis_penerimaan }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <p class="small text-left">
            <strong>Catatan:</strong> Kwitansi ini dianggap sah setelah diterima dan dibukukan.
        </p>
    </div>

    <table class="signature-table">
        <tr>
            <td>Mengetahui,</td>
            <td>Dibuat Oleh,</td>
            <td>Disetor Oleh,</td>
        </tr>
        <tr>
            <td height="60px">(........................................)</td>
            <td>(........................................)</td>
            <td>(........................................)</td>
        </tr>
        <tr>
            <td class="small">Direktur</td>
            <td class="small">Kasir</td>
            <td class="small">Penyetor</td>
        </tr>
    </table>

</body>
</html>
