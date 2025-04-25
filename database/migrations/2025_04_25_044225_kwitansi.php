<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kwitansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_kas_id')->constrained('transaksi_kas')->onDelete('cascade');
            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');
            $table->string('no_doc')->unique(); // contoh: 22/CI-GBA/THN 2024
            $table->date('tanggal');
    
            $table->string('dari'); // nama pembayar
            $table->decimal('jumlah', 20, 2);
            $table->text('untuk_pembayaran');
            $table->enum('jenis_penerimaan', ['Tunai', 'Transfer Bank','Giro','Cek','Draft']);
    
            $table->string('dibuat_oleh')->nullable();
            $table->string('disetor_oleh')->nullable();
            $table->string('mengetahui')->nullable();
    
            $table->timestamps();
        });
    }
    
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Kwitansi');
    }
};
