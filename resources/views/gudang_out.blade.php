<!DOCTYPE html>
<html>
<head>
    <title>Input Gudang Out</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <h1>Input Gudang Out</h1>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif
    <form action="/gudang-out" method="POST">
        @csrf

        <label>Jenis Peralatan:</label>
        <select id="jenis_peralatan" name="jenis_peralatan">
            <option value="">-- Pilih Jenis Peralatan --</option>
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
        </select>
        <br>

        <label>Kode Barang:</label>
        <select id="kode_barang" name="kode_barang">
            <option value="">-- Pilih Kode Barang --</option>
        </select><br>

        <label>Nama Barang:</label>
        <input type="text" id="nama_barang" name="nama_barang" readonly><br>

        <!-- Input lainnya -->
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

    <script>
        $(document).ready(function() {
            $('#jenis_peralatan').change(function() {
                var jenisPeralatan = $(this).val();
                $('#kode_barang').empty().append('<option value="">-- Pilih Kode Barang --</option>');
                $('#nama_barang').val('');

                if (jenisPeralatan) {
                    $.ajax({
                        url: '/get-stock-codes/' + jenisPeralatan,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(index, value) {
                                $('#kode_barang').append('<option value="' + value.kode + '">' + value.kode + '</option>');
                            });
                        }
                    });
                }
            });

            $('#kode_barang').change(function() {
                var selectedKode = $(this).val();

                $.ajax({
                    url: '/get-stock-codes/' + $('#jenis_peralatan').val(),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $.each(data, function(index, value) {
                            if (value.kode === selectedKode) {
                                $('#nama_barang').val(value.nama_barang);
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
