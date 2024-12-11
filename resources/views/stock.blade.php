<!DOCTYPE html>
<html>
<head>
    <title>Input Stock</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Input Stock</h1>
    <div id="success-message"></div>

    <form id="stock-form" action="/stock" method="POST">
        @csrf
    
        <label>Jenis Peralatan:</label>
        <select id="jenis-peralatan" name="jenis_peralatan" required>
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

    <h2>Data Barang</h2>
    <table border="1" id="stock-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data akan dimuat di sini -->
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            // Fungsi untuk memuat data berdasarkan jenis peralatan
            function loadStockData(type) {
                $.ajax({
                    url: `/stock-codes/${type}`,
                    method: 'GET',
                    success: function(data) {
                        const tableBody = $('#stock-table tbody');
                        tableBody.empty(); // Hapus data lama
                        data.forEach(item => {
                            tableBody.append(`<tr>
                                <td>${item.kode}</td>
                                <td>${item.nama_barang}</td>
                            </tr>`);
                        });
                    },
                    error: function(err) {
                        console.error('Error:', err);
                    }
                });
            }

            // Muat data saat jenis peralatan berubah
            $('#jenis-peralatan').change(function() {
                const selectedType = $(this).val();
                loadStockData(selectedType);
            });

            // Kirim form menggunakan AJAX
            $('#stock-form').submit(function(e) {
                e.preventDefault(); // Cegah reload halaman
                $.ajax({
                    url: '/stock',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#success-message').html(`<p style="color: green;">${response.message}</p>`);
                        const selectedType = $('#jenis-peralatan').val();
                        loadStockData(selectedType); // Perbarui tabel setelah menyimpan
                        $('#stock-form')[0].reset(); // Reset form
                    },
                    error: function(err) {
                        console.error('Error:', err);
                    }
                });
            });

            // Muat data pertama kali saat halaman dibuka
            loadStockData($('#jenis-peralatan').val());
        });
    </script>
</body>
</html>
