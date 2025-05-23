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
            $table->date('tanggal');
            $table->enum('kode', ['101', '102']); 
            $table->string('status')->default('pending');
            $table->decimal('jumlah', 18, 2); 
            $table->decimal('saldo_setelah_transaksi', 15, 2)->nullable(); 
            $table->enum('metode_pembayaran', ['Cash', 'Transfer Bank', 'Giro', 'Cek', 'Draft'])->default('Cash');
            $table->string('dibuat_oleh'); 
            $table->text('keterangan_objek_transaksi')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_kas');
    }
};
