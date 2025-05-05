<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sttb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_in_id')->constrained('gudang_in')->onDelete('cascade');
            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');
            $table->string('no_doc')->unique();
            $table->date('tanggal');
            $table->string('nama_supplier');
            $table->string('nama_barang');
            $table->string('jumlah');
            $table->string('satuan');
            $table->enum('jenis_penerimaan', ['Langsung', 'Tidak Langsung', 'Ambil Sendiri']);
            $table->string('diterima_oleh');
            $table->string('diserahkan_oleh');
            $table->string('mengetahui')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sttb');
    }
};
