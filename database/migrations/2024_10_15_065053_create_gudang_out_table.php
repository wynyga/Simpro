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
        Schema::create('gudang_out', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');  // Referensi ke kode barang dari tabel stock
            $table->string('nama_barang');
            $table->date('tanggal');
            $table->string('peruntukan');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->decimal('jumlah_harga', 18, 2);
            $table->text('keterangan')->nullable();  // Bisa kosong
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gudang_out');
    }
};
