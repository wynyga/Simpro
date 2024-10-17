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

        <label>Jenis Biaya:</label>
        <select name="jenis_biaya" id="jenis-biaya-select" required>
            <option value="">-- Pilih Jenis Biaya --</option>
            <option value="KAS PROJECT / KAS MASUK MINGGU INI">KAS PROJECT / KAS MASUK MINGGU INI</option>
        </select><br>

        <!-- Bagian Uraian -->
        <div id="uraian-section" style="display: none;">
            <h3>Uraian</h3>
            <label>Pilihan Uraian:</label>
            <select id="uraian-choices" name="uraian">
                <option value="">-- Pilih Uraian --</option>
                <option value="saldo_sisa">Saldo sisa Kas Proyek Minggu sebelumnya</option>
                <option value="penerimaan_kas">Penerimaan Kas Minggu Ini</option>
            </select><br>
        </div>

        <!-- Bagian Penerimaan Kas -->
        <div id="kategori-section" style="display: none;">
            <h4>Kategori</h4>
            <label>Pilihan Kategori:</label>
            <select id="kategori-choices" name="kategori">
                <option value="">-- Pilih Kategori --</option>
                <option value="operasional_proyek">Penerimaan dari Operasional Proyek</option>
                <option value="dana_tunai_lainnya">Penerimaan dana Tuni lainnya</option>
                <option value="penerimaan_kpr">Penerimaan KPR</option>
                <option value="share_capital">Share Capital Ordinary</option>
            </select><br>
        </div>

        <!-- Bagian Penerimaan dari Operasional Proyek -->
        <div id="operasional-proyek-section" style="display: none;">
            <h4>Sub Kategori: Penerimaan dari Operasional Proyek</h4>
            <select id="operasional-proyek-choices" name="sub_kategori" class="sub-kategori-input">
                <option value="">-- Pilih Sub Kategori --</option>
                <option value="booking_fee">Penerimaan Booking Fee</option>
                <option value="down_payment">Penerimaan dari Down Payment</option>
            </select><br>
        </div>

        <!-- Bagian Penerimaan dana Tuni lainnya -->
        <div id="dana-tunai-section" style="display: none;">
            <h4>Sub Kategori: Penerimaan dana Tuni lainnya</h4>
            <select id="dana-tunai-choices" name="sub_kategori" class="sub-kategori-input">
                <option value="kelebihan_tanah">Biaya Kelebihan Tanah</option>
                <option value="penambahan_spek">Biaya Penambahan Spek Bangunan</option>
                <option value="selisih_kpr">Biaya Selisih KPR</option>
            </select><br>
        </div>

        <!-- Bagian input akhir -->
        <div id="final-input-section" style="display: none;">
            <label>Jumlah (RP):</label>
            <input type="number" step="0.01" name="total" required><br>

            <label>Keterangan:</label>
            <input type="text" name="deskripsi"><br>
        </div>

        <button type="submit" id="submit-button" style="display: none;">Simpan</button>
    </form>

    <script>
        $(document).ready(function() {
            // Event untuk memilih jenis biaya
            $('#jenis-biaya-select').change(function() {
                if ($(this).val() === 'KAS PROJECT / KAS MASUK MINGGU INI') {
                    $('#uraian-section').show();
                } else {
                    $('#uraian-section, #kategori-section, #final-input-section, #operasional-proyek-section, #dana-tunai-section, #submit-button').hide();
                }
            });

            // Event untuk memilih Uraian
            $('#uraian-choices').change(function() {
                var uraianChoice = $(this).val();
                if (uraianChoice === 'penerimaan_kas') {
                    $('#kategori-section').show();
                    $('#final-input-section, #submit-button').hide();
                } else if (uraianChoice === 'saldo_sisa') {
                    $('#final-input-section, #submit-button').show();
                    $('#kategori-section, #operasional-proyek-section, #dana-tunai-section').hide();
                } else {
                    $('#final-input-section, #kategori-section, #operasional-proyek-section, #dana-tunai-section, #submit-button').hide();
                }
            });

            // Event untuk memilih Kategori
            $('#kategori-choices').change(function() {
                var kategoriChoice = $(this).val();
                // Hide all sub-kategori sections
                $('.sub-kategori-input').prop('disabled', true).closest('div').hide();
                
                if (kategoriChoice === 'operasional_proyek') {
                    $('#operasional-proyek-section').show().find('.sub-kategori-input').prop('disabled', false);
                    $('#final-input-section, #submit-button').hide();
                } else if (kategoriChoice === 'dana_tunai_lainnya') {
                    $('#dana-tunai-section').show().find('.sub-kategori-input').prop('disabled', false);
                    $('#final-input-section, #submit-button').hide();
                } else {
                    $('#final-input-section, #submit-button').show();
                }
            });

            // Event untuk memilih Sub Kategori di Operasional Proyek dan Dana Tunai Lainnya
            $('#operasional-proyek-choices, #dana-tunai-choices').change(function() {
                $('#final-input-section, #submit-button').show();
            });
        });
    </script>
</body>
</html>
