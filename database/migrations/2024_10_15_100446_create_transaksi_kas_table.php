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
            $table->date('tanggal');  // Tanggal transaksi
            $table->string('keterangan_transaksi');  // Keterangan transaksi
            $table->enum('kode', ['101', '102']);  // 101 untuk cash in, 102 untuk cash out
            $table->decimal('jumlah', 18, 2);  // Jumlah dalam Rupiah
            $table->text('keterangan_objek_transaksi')->nullable();  // Keterangan objek transaksi
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_kas');
    }
};
