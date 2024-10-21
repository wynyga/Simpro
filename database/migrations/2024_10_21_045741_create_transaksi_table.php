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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_blok_unit')->constrained('blok_unit')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('user_perumahan')->onDelete('cascade');
            $table->decimal('harga_jual_standar', 15, 2);
            $table->decimal('kelebihan_tanah', 15, 2)->nullable();
            $table->decimal('penambahan_luas_bangunan', 15, 2)->nullable();
            $table->decimal('perubahan_spek_bangunan', 15, 2)->nullable();
            $table->decimal('total_harga_jual', 15, 2);
            $table->enum('kpr_disetujui', ['Ya', 'Tidak'])->default('Tidak');
            $table->decimal('minimum_dp', 15, 2);
            $table->decimal('kewajiban_hutang', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};
