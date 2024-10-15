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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');   // Menyimpan kode barang yang tidak ditemukan
            $table->string('nama_barang')->nullable();   // Opsional: Nama barang
            $table->string('tipe_log')->default('error');  // Jenis log, bisa "error" atau lainnya
            $table->text('pesan');  // Pesan error atau detail log
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
