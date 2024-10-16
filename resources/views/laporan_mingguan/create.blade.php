<!DOCTYPE html>
<html>
<head>
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
    
        <div id="kas-project-section" style="display: none;">
            <h3>KAS PROJECT / KAS MASUK MINGGU INI</h3>
            <label>Pilihan Kas Project:</label>
            <select id="kas-project-choices" name="kas_project_choice">
                <option value="saldo_sisa">Saldo sisa Kas Proyek Minggu sebelumnya</option>
                <option value="penerimaan_kas">Penerimaan Kas Minggu Ini</option>
            </select><br>
        </div>
    
        <div id="penerimaan-kas-section" style="display: none;">
            <h4>Penerimaan Kas Minggu Ini</h4>
            <select id="penerimaan-kas-choices" name="penerimaan_kas_choice">
                <option value="operasional_proyek">Penerimaan dari Operasional Proyek</option>
                <option value="dana_tunai_lainnya">Penerimaan dana Tuni lainnya</option>
                <option value="penerimaan_kpr">Penerimaan KPR</option>
                <option value="share_capital">Share Capital Ordinary</option>
            </select><br>
        </div>
    
        <div id="operasional-proyek-section" style="display: none;">
            <h4>Penerimaan dari Operasional Proyek</h4>
            <select id="operasional-proyek-choices" name="operasional_proyek_choice">
                <option value="booking_fee">Penerimaan Booking Fee</option>
                <option value="down_payment">Penerimaan dari Down Payment</option>
            </select><br>
        </div>
    
        <div id="dana-tunai-section" style="display: none;">
            <h4>Penerimaan dana Tuni lainnya</h4>
            <select id="dana-tunai-choices" name="dana_tunai_choice">
                <option value="kelebihan_tanah">Biaya Kelebihan Tanah</option>
                <option value="penambahan_spek">Biaya Penambahan Spek Bangunan</option>
                <option value="selisih_kpr">Biaya Selisih KPR</option>
            </select><br>
        </div>
    
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
            $('#kategori-select').change(function() {
                if ($(this).val() === 'A. Kas Project/Kas Masuk Minggu Ini') {
                    $('#kas-project-section').show();
                } else {
                    $('#kas-project-section, #penerimaan-kas-section, #final-input-section').hide();
                }
            });
    
            $('#kas-project-choices').change(function() {
                var kasProjectChoice = $(this).val();
                if (kasProjectChoice === 'penerimaan_kas') {
                    $('#penerimaan-kas-section').show();
                } else {
                    $('#penerimaan-kas-section, #final-input-section').hide();
                }
            });
    
            $('#penerimaan-kas-choices').change(function() {
                $('#final-input-section').show();
            });
        });
    </script>
    
    
</body>
</html>
