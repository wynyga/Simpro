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
        Schema::create('laporan_mingguan', function (Blueprint $table) {
            $table->id();
            $table->integer('minggu_ke');  // Minggu ke
            $table->integer('tahun_ke');   // Tahun ke
            $table->string('code', 10);    // Kode kombinasi Minggu dan Tahun, contoh: M624
            $table->string('jenis_biaya');
            $table->string('uraian');      // Uraian atau deskripsi dari laporan
            $table->string('kategori')->nullable(); // Kategori (misalnya KAS PROJECT, KAS KELUAR, dll.)
            $table->string('sub_kategori')->nullable();
            $table->string('code_account');  // Kode account seperti KI0101M624, dll.
            $table->decimal('total', 18, 2)->nullable();  // Total nilai (RP)
            $table->string('deskripsi')->nullable();  // Kode account seperti KI0101M624, dll.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan_mingguan');
    }
};
