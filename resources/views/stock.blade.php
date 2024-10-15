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
    
        <label>Jenis Peralatan:</label>
        <select name="jenis_peralatan" required>
            <option value="day_work">Day Work</option>
            <option value="equipment">Equipment</option>
            <option value="tools">Tools</option>
            <option value="land_stone_sand">Land, Stone, Sand</option>
            <option value="cement">Cement</option>
            <option value="rebar">Rebar</option>
            <option value="wood">Wood</option>
            <option value="roof_ceiling_tile">Roof, Ceiling, Tile</option>
            <option value="keramik_floor">Keramik, Floor</option>
            <option value="paint_glass_wallpaper">Paint, Glass, Wallpaper</option>
            <option value="others">Others</option>
            <option value="oil_chemical_perekat">Oil, Chemical, Perekat</option>
            <option value="sanitary">Sanitary</option>
            <option value="piping_pump">Piping, Pump</option>
            <option value="lighting">Lighting</option>
        </select><br>
    
        <!-- Form kode barang dihapus karena digenerate otomatis -->
    
        <label>Nama Barang:</label>
        <input type="text" name="nama_barang" required><br>
    
        <label>UTY:</label>
        <input type="text" name="uty" required><br>
    
        <label>Satuan:</label>
        <input type="text" name="satuan" required><br>
    
        <label>Harga Satuan:</label>
        <input type="number" name="harga_satuan" step="0.01" required><br>
    
        <label>Stock Bahan:</label>
        <input type="number" name="stock_bahan" required><br>
    
        <button type="submit">Simpan</button>
    </form>
    
    
    
</body>
</html>
