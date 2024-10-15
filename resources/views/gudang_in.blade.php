<!DOCTYPE html>
<html>
<head>
    <title>Input Gudang In</title>
</head>
<body>
    <h1>Input Gudang In</h1>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif
    <form action="/gudang-in" method="POST">
        @csrf
        <label>Kode Barang:</label>
        <input type="text" name="kode_barang"><br>

        <label>Nama Barang:</label>
        <input type="text" name="nama_barang"><br>

        <label>Pengirim:</label>
        <input type="text" name="pengirim"><br>

        <label>No Nota:</label>
        <input type="text" name="no_nota"><br>

        <label>Tanggal Barang Masuk:</label>
        <input type="date" name="tanggal_barang_masuk"><br>

        <label>Jumlah:</label>
        <input type="number" name="jumlah"><br>

        <label>Satuan:</label>
        <input type="text" name="satuan"><br>

        <label>Harga Satuan:</label>
        <input type="number" step="0.01" name="harga_satuan"><br>

        <label>Jumlah Harga:</label>
        <input type="number" step="0.01" name="jumlah_harga"><br>

        <label>Keterangan:</label>
        <textarea name="keterangan"></textarea><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>
