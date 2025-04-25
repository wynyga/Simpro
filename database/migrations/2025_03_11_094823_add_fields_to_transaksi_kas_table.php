<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->decimal('saldo_setelah_transaksi', 15, 2)->after('jumlah')->nullable(); // Menyimpan saldo setelah transaksi
            $table->enum('metode_pembayaran', ['Tunai', 'Transfer Bank','Giro','Cek','Draft'])->after('saldo_setelah_transaksi')->default('Tunai'); // Dropdown metode pembayaran
            $table->string('dibuat_oleh')->after('metode_pembayaran'); // Nama user yang membuat transaksi
        });
    }

    public function down()
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropColumn(['saldo_setelah_transaksi', 'metode_pembayaran', 'dibuat_oleh']);
        });
    }
};
