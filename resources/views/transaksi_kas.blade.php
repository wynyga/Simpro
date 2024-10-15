<!DOCTYPE html>
<html>
<head>
    <title>Transaksi KAS</title>
</head>
<body>
    <h1>Input Transaksi KAS</h1>
    <form action="/transaksi-kas" method="POST">
        @csrf

        <label>Tanggal:</label>
        <input type="date" name="tanggal" required><br>

        <label>Keterangan Transaksi:</label>
        <input type="text" name="keterangan_transaksi" required><br>

        <label>Kode:</label>
        <select name="kode" required>
            <option value="101">101 - Cash In</option>
            <option value="102">102 - Cash Out</option>
        </select><br>

        <label>Jumlah (RP):</label>
        <input type="number" step="0.01" name="jumlah" required><br>

        <label>Keterangan Objek Transaksi:</label>
        <textarea name="keterangan_objek_transaksi"></textarea><br>

        <button type="submit">Simpan</button>
    </form>

    <hr>

    <h2>Saldo Kas</h2>
    <p>Total Cash In: Rp. {{ number_format($totalCashIn, 2) }}</p>
    <p>Total Cash Out: Rp. {{ number_format($totalCashOut, 2) }}</p>
    <p>Saldo Kas: Rp. {{ number_format($saldoKas, 2) }}</p>

    <hr>

    <h2>Riwayat Transaksi KAS</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan Transaksi</th>
                <th>Kode</th>
                <th>Jumlah (RP)</th>
                <th>Keterangan Objek Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksiKas as $transaksi)
                <tr>
                    <td>{{ $transaksi->tanggal }}</td>
                    <td>{{ $transaksi->keterangan_transaksi }}</td>
                    <td>{{ $transaksi->kode }}</td>
                    <td>{{ number_format($transaksi->jumlah, 2) }}</td>
                    <td>{{ $transaksi->keterangan_objek_transaksi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
