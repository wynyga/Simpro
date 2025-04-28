<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal'); // Tanggal transaksi
            $table->enum('kode', ['101', '102']); // 101 untuk cash in, 102 untuk cash out
            $table->string('status')->default('pending');
            $table->decimal('jumlah', 18, 2); // Jumlah dalam Rupiah
            $table->decimal('saldo_setelah_transaksi', 15, 2)->nullable(); // Saldo setelah transaksi
            $table->enum('metode_pembayaran', ['Tunai', 'Transfer Bank', 'Giro', 'Cek', 'Draft'])->default('Tunai');
            $table->string('dibuat_oleh'); // Nama user
            $table->text('keterangan_objek_transaksi')->nullable(); // Keterangan objek transaksi
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_kas');
    }
};
