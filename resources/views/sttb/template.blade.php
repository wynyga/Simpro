<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>STTB - {{ $sttb->no_doc }}</title>
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
        <h2>BUKTI PENERIMAAM BARANG DARI SUPLAYER / VENDOR</h2>
        <p>No. Dokumen: {{ $sttb->no_doc }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($sttb->tanggal)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="section">
        <table>
            <tr>
                <td width="30%">Nama Barang</td>
                <td>: {{ $sttb->nama_barang }}</td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td>: {{ $sttb->jumlah }} {{ $sttb->satuan }}</td>
            </tr>
            <tr>
                <td>Sistem Pembayaran</td>
                <td>: {{ $sttb->gudangIn->sistem_pembayaran ?? '-' }}</td>
            </tr> 
            <tr>
                <td>Keterangan Barang</td>
                <td>: {{ $sttb->gudangIn->keterangan ?? '-' }}</td>
            </tr>           
        </table>
    </div>

    <div class="section">
        <p>
            Telah diterima barang sesuai dengan informasi di atas dari pihak pengirim berikut:
        </p>
        <table>
            <tr>
                <td width="30%">Sudah Terima Dari</td>
                <td>: {{ $sttb->diserahkan_oleh }}</td>
            </tr>
            <tr>
                <td>Diterima Oleh</td>
                <td>: {{ $sttb->diterima_oleh }}</td>
            </tr>
        </table>
    </div>

    <table class="signature-table">
        <tr>
            <td>Mengetahui,</td>
            <td>Dibuat Oleh,</td>
            <td>Disetor Oleh,</td>
        </tr>
        <tr style="height: 60px;">
            <td style="padding-top: 40px;">( ............................ )</td>
            <td style="padding-top: 40px;">( ............................ )</td>
            <td style="padding-top: 40px;">( ............................ )</td>
        </tr>
        <tr>
            <td class="small">Penerima Barang</td>
            <td class="small">Pemesan/Account</td>
            <td class="small">Customer/Vendor</td>
        </tr>
    </table>

</body>
</html>
