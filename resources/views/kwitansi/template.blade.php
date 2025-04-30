<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Kwitansi - {{ $kwitansi->no_doc }}</title>
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
            <h2>KWITANSI</h2>
            <p>No. Doc: {{ $kwitansi->no_doc }}</p>
            <p>Tanggal: {{ \Carbon\Carbon::parse($kwitansi->tanggal)->format('d M Y') }}</p>
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
                <td class="small" style="margin-top: 10px;">Direktur</td>
                <td class="small" style="margin-top: 10px;">Kasir</td>
                <td class="small" style="margin-top: 10px;">Penyetor</td>
            </tr>
        </table>

    </body>
</html>
