<!DOCTYPE html>
<html>
<head>
    <title>Input Gudang In</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <h1>Input Gudang In</h1>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif
    <form action="/gudang-in" method="POST">
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
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
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

        // Menambahkan event listener untuk menangkap data form saat submit
        $('form').submit(function(event) {
            event.preventDefault(); // Mencegah form dari submit otomatis

            // Mengambil semua data form
            var formData = {
                jenis_peralatan: $('#jenis_peralatan').val(),
                kode_barang: $('#kode_barang').val(),
                nama_barang: $('#nama_barang').val(),
                pengirim: $('input[name="pengirim"]').val(),
                no_nota: $('input[name="no_nota"]').val(),
                tanggal_barang_masuk: $('input[name="tanggal_barang_masuk"]').val(),
                jumlah: $('input[name="jumlah"]').val(),
                satuan: $('input[name="satuan"]').val(),
                harga_satuan: $('input[name="harga_satuan"]').val(),
                jumlah_harga: $('input[name="jumlah_harga"]').val(),
                keterangan: $('textarea[name="keterangan"]').val()
            };

            // Menampilkan data form di console
            console.log('Form Data:', formData);

            // Menyelesaikan pengiriman form setelah log data
            this.submit();
        });
    });

    </script>
    
</body>
</html>
