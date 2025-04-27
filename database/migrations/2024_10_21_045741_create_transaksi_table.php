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
            $table->foreignId('unit_id')->constrained('unit')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('user_perumahan')->onDelete('cascade');
            $table->decimal('harga_jual_standar', 15, 2);
            $table->decimal('kelebihan_tanah', 15, 2)->default(0);
            $table->decimal('penambahan_luas_bangunan', 15, 2)->default(0);
            $table->decimal('perubahan_spek_bangunan', 15, 2)->default(0);
            $table->decimal('total_harga_jual', 15, 2);
            $table->enum('kpr_disetujui', ['Ya', 'Tidak'])->default('Tidak');
            $table->decimal('minimum_dp', 15, 2);
            $table->decimal('plafon_kpr', 15, 2);
            $table->decimal('biaya_booking', 15, 2)->nullable(); // Booking fee optional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};
