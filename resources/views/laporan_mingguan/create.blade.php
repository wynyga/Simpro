<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Laporan Mingguan</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Input Laporan Mingguan</h1>

    <form action="{{ route('laporan_mingguan.store') }}" method="POST">
        @csrf

        <label>Minggu Ke:</label>
        <input type="number" name="minggu_ke" required><br>

        <label>Tahun Ke:</label>
        <input type="number" name="tahun_ke" required><br>

        <!-- Jenis Biaya -->
        <label>Jenis Biaya:</label>
        <select name="jenis_biaya" id="jenis-biaya-select" required>
            <option value="">-- Pilih Jenis Biaya --</option>
            <option value="KAS PROJECT / KAS MASUK MINGGU INI">KAS PROJECT / KAS MASUK MINGGU INI</option>
        </select><br>

        <!-- Uraian -->
        <div id="uraian-section" style="display: none;">
            <label>Uraian:</label>
            <select name="uraian" id="uraian-choices" required>
                <option value="">-- Pilih Uraian --</option>
                <option value="saldo_sisa">Saldo sisa Kas Proyek Minggu sebelumnya</option>
                <option value="penerimaan_kas">Penerimaan Kas Minggu Ini</option>
            </select><br>
        </div>

        <!-- Kategori -->
        <div id="kategori-section" style="display: none;">
            <label>Kategori:</label>
            <select name="kategori" id="kategori-choices">
                <option value="">-- Pilih Kategori --</option>
                <option value="operasional_proyek">Penerimaan dari Operasional Proyek</option>
                <option value="dana_tunai_lainnya">Penerimaan dana Tuni lainnya</option>
                <option value="penerimaan_kpr">Penerimaan KPR</option>
                <option value="share_capital">Share Capital Ordinary</option>
            </select><br>
        </div>

        <!-- Sub Kategori -->
        <div id="sub-kategori-section" style="display: none;">
            <label>Sub Kategori:</label>
            <select name="sub_kategori" id="sub-kategori-choices">
                <option value="">-- Pilih Sub Kategori --</option>
                <option value="booking_fee">Penerimaan Booking Fee</option>
                <option value="down_payment">Penerimaan dari Down Payment</option>
                <option value="kelebihan_tanah">Biaya Kelebihan Tanah</option>
                <option value="penambahan_spek">Biaya Penambahan Spek Bangunan</option>
                <option value="selisih_kpr">Biaya Selisih KPR</option>
            </select><br>
        </div>

        <!-- Jumlah -->
        <label>Jumlah (RP):</label>
        <input type="number" name="total" step="0.01" required><br>

        <!-- Deskripsi -->
        <label>Deskripsi:</label>
        <input type="text" name="deskripsi"><br>

        <button type="submit">Simpan</button>
    </form>

    <script>
        $(document).ready(function() {
            // Event untuk memilih jenis biaya
            $('#jenis-biaya-select').change(function() {
                if ($(this).val() === 'KAS PROJECT / KAS MASUK MINGGU INI') {
                    $('#uraian-section').show();
                } else {
                    $('#uraian-section, #kategori-section, #sub-kategori-section').hide();
                }
            });

            // Event untuk memilih Uraian
            $('#uraian-choices').change(function() {
                var uraianChoice = $(this).val();
                if (uraianChoice === 'penerimaan_kas') {
                    $('#kategori-section').show();
                } else {
                    $('#kategori-section, #sub-kategori-section').hide();
                }
            });

            // Event untuk memilih Kategori
            $('#kategori-choices').change(function() {
                var kategoriChoice = $(this).val();
                if (kategoriChoice === 'operasional_proyek' || kategoriChoice === 'dana_tunai_lainnya') {
                    $('#sub-kategori-section').show();
                } else {
                    $('#sub-kategori-section').hide();
                }
            });
        });
    </script>
</body>
</html>
