<!DOCTYPE html>
<html>
<head>
    <title>Input Stock</title>
</head>
<body>
    <h1>Input Stock</h1>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif
    <form action="/stock" method="POST">
        @csrf
        <label>Kode Barang:</label>
        <input type="text" name="kode"><br>

        <label>Nama Barang:</label>
        <input type="text" name="nama_barang"><br>

        <label>UTY:</label>
        <input type="text" name="uty"><br>

        <label>Satuan:</label>
        <input type="text" name="satuan"><br>

        <label>Harga Satuan:</label>
        <input type="number" step="0.01" name="harga_satuan"><br>

        <label>Stock Bahan:</label>
        <input type="number" name="stock_bahan"><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>
