<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mingguan - Kas Project</title>
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

        <label>Kategori:</label>
        <select name="kategori" id="kategori-select" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="A. Kas Project/Kas Masuk Minggu Ini">A. Kas Project/Kas Masuk Minggu Ini</option>
        </select><br>

        <!-- Bagian Kas Project -->
        <div id="kas-project-section" style="display: none;">
            <h3>KAS PROJECT / KAS MASUK MINGGU INI</h3>
            <label>Pilihan Kas Project:</label>
            <select id="kas-project-choices" name="kas_project_choice">
                <option value="">-- Pilih --</option>
                <option value="saldo_sisa">Saldo sisa Kas Proyek Minggu sebelumnya</option>
                <option value="penerimaan_kas">Penerimaan Kas Minggu Ini</option>
            </select><br>
        </div>

        <!-- Bagian Penerimaan Kas -->
        <div id="penerimaan-kas-section" style="display: none;">
            <h4>Penerimaan Kas Minggu Ini</h4>
            <label>Pilihan Penerimaan:</label>
            <select id="penerimaan-kas-choices" name="penerimaan_kas_choice">
                <option value="">-- Pilih --</option>
                <option value="operasional_proyek">Penerimaan dari Operasional Proyek</option>
                <option value="dana_tunai_lainnya">Penerimaan dana Tuni lainnya</option>
                <option value="penerimaan_kpr">Penerimaan KPR</option>
                <option value="share_capital">Share Capital Ordinary</option>
            </select><br>
        </div>

        <!-- Bagian Penerimaan dari Operasional Proyek -->
        <div id="operasional-proyek-section" style="display: none;">
            <h4>Penerimaan dari Operasional Proyek</h4>
            <label>Pilihan Operasional Proyek:</label>
            <select id="operasional-proyek-choices" name="operasional_proyek_choice">
                <option value="">-- Pilih --</option>
                <option value="booking_fee">Penerimaan Booking Fee</option>
                <option value="down_payment">Penerimaan dari Down Payment</option>
            </select><br>
        </div>

        <!-- Bagian Penerimaan dana Tuni lainnya -->
        <div id="dana-tunai-section" style="display: none;">
            <h4>Penerimaan dana Tuni lainnya</h4>
            <label>Pilihan Dana Tunai Lainnya:</label>
            <select id="dana-tunai-choices" name="dana_tunai_choice">
                <option value="">-- Pilih --</option>
                <option value="kelebihan_tanah">Biaya Kelebihan Tanah</option>
                <option value="penambahan_spek">Biaya Penambahan Spek Bangunan</option>
                <option value="selisih_kpr">Biaya Selisih KPR</option>
            </select><br>
        </div>

        <!-- Bagian input akhir -->
        <div id="final-input-section" style="display: none;">
            <label>Jumlah (RP):</label>
            <input type="number" step="0.01" name="jumlah_transaksi" required><br>

            <label>Keterangan:</label>
            <input type="text" name="keterangan_transaksi"><br>
        </div>

        <button type="submit">Simpan</button>
    </form>

    <script>
        $(document).ready(function() {
            // Event untuk memilih kategori Kas Project
            $('#kategori-select').change(function() {
                if ($(this).val() === 'A. Kas Project/Kas Masuk Minggu Ini') {
                    $('#kas-project-section').show();
                } else {
                    $('#kas-project-section, #penerimaan-kas-section, #final-input-section, #operasional-proyek-section, #dana-tunai-section').hide();
                }
            });

            // Event untuk memilih Kas Project
            $('#kas-project-choices').change(function() {
                var kasProjectChoice = $(this).val();
                if (kasProjectChoice === 'penerimaan_kas') {
                    $('#penerimaan-kas-section').show();
                    $('#final-input-section').hide();
                } else if (kasProjectChoice === 'saldo_sisa') {
                    $('#final-input-section').show();
                    $('#penerimaan-kas-section, #operasional-proyek-section, #dana-tunai-section').hide();
                } else {
                    $('#final-input-section, #penerimaan-kas-section, #operasional-proyek-section, #dana-tunai-section').hide();
                }
            });

            // Event untuk memilih Penerimaan Kas
            $('#penerimaan-kas-choices').change(function() {
                var penerimaanChoice = $(this).val();
                if (penerimaanChoice === 'operasional_proyek') {
                    $('#operasional-proyek-section').show();
                    $('#dana-tunai-section, #final-input-section').hide();
                } else if (penerimaanChoice === 'dana_tunai_lainnya') {
                    $('#dana-tunai-section').show();
                    $('#operasional-proyek-section, #final-input-section').hide();
                } else {
                    $('#final-input-section').show();
                    $('#operasional-proyek-section, #dana-tunai-section').hide();
                }
            });

            // Event untuk memilih detail di Operasional Proyek dan Dana Tunai Lainnya
            $('#operasional-proyek-choices, #dana-tunai-choices').change(function() {
                $('#final-input-section').show();
            });
        });
    </script>
</body>
</html>
