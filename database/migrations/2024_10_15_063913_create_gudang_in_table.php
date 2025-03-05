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
        Schema::create('gudang_in', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');  // Referensi ke kode barang dari tabel stock
            $table->string('nama_barang');
            $table->string('pengirim')->nullable();  // Bisa kosong
            $table->string('no_nota');
            $table->date('tanggal_barang_masuk');
            $table->string('status')->default('pending');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('jumlah_harga', 18, 2);
            $table->text('keterangan')->nullable();  // Bisa kosong
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gudang_in');
    }
};
