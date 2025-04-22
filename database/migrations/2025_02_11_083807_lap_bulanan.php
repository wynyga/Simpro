<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lap_bulanan', function (Blueprint $table) {
            $table->id();

            // Relasi ke cost tee
            $table->unsignedBigInteger('cost_tee_id');
            $table->foreign('cost_tee_id')->references('id')->on('cost_tees')->onDelete('cascade');

            // Relasi ke perumahan (penting untuk filtering user)
            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');

            $table->integer('bulan'); // 1-12
            $table->integer('tahun'); // Tahun laporan
            $table->string('status')->default('pending');
            $table->decimal('jumlah', 15, 2); // Total transaksi
            $table->string('code_account')->index(); // Bisa jadi kode unik kas atau nomor referensi

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lap_bulanan');
    }
};
