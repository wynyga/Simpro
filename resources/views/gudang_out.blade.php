<!DOCTYPE html>
<html>
<head>
    <title>Input Gudang Out</title>
</head>
<body>
    <h1>Input Gudang Out</h1>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif
    <form action="/gudang-out" method="POST">
        @csrf
        <label>Kode Barang:</label>
        <input type="text" name="kode_barang"><br>

        <label>Nama Barang:</label>
        <input type="text" name="nama_barang"><br>

        <label>Tanggal:</label>
        <input type="date" name="tanggal"><br>

        <label>Peruntukan:</label>
        <input type="text" name="peruntukan"><br>

        <label>Jumlah:</label>
        <input type="number" name="jumlah"><br>

        <label>Satuan:</label>
        <input type="text" name="satuan"><br>

        <label>Jumlah Harga:</label>
        <input type="number" step="0.01" name="jumlah_harga"><br>

        <label>Keterangan:</label>
        <textarea name="keterangan"></textarea><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>
