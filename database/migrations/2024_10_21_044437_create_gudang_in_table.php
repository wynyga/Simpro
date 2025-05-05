<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gudang_in', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('pengirim')->nullable();
            $table->string('no_nota');
            $table->date('tanggal_barang_masuk');
            $table->string('sistem_pembayaran');
            $table->string('status')->default('pending');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('jumlah_harga', 18, 2);
            $table->text('keterangan')->nullable();
            $table->enum('jenis_penerimaan', ['Langsung', 'Tidak Langsung', 'Ambil Sendiri'])->nullable();
           // $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gudang_in');
    }
};
