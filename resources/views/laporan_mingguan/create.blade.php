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
    <select name="jenis_biaya" required>
        <option value="">-- Pilih Jenis Biaya --</option>
        <option value="KAS PROJECT / KAS MASUK MINGGU INI">KAS PROJECT / KAS MASUK MINGGU INI</option>
    </select><br>

    <label>Uraian:</label>
    <select name="uraian" required>
        <option value="saldo_sisa">Saldo sisa Kas Proyek Minggu sebelumnya</option>
        <option value="penerimaan_kas">Penerimaan Kas Minggu Ini</option>
    </select><br>

    <label>Kategori:</label>
    <select name="kategori" required>
        <option value="operasional_proyek">Penerimaan dari Operasional Proyek</option>
        <option value="dana_tunai_lainnya">Penerimaan dana Tuni lainnya</option>
    </select><br>

    <label>Sub Kategori:</label>
    <select name="sub_kategori" required>
        <option value="booking_fee">Penerimaan Booking Fee</option>
        <option value="down_payment">Penerimaan dari Down Payment</option>
    </select><br>

    <label>Jumlah (RP):</label>
    <input type="number" name="total" step="0.01" required><br>

    <label>Deskripsi:</label>
    <input type="text" name="deskripsi" required><br>

    <button type="submit">Simpan</button>
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
