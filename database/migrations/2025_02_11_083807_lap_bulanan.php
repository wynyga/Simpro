<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lap_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_structure_id')->constrained('cost_structures')->onDelete('cascade');
            $table->integer('bulan'); // 1-12
            $table->integer('tahun'); // Tahun laporan
            $table->string('status')->default('pending');
            $table->decimal('jumlah', 15, 2); // Total transaksi
            $table->string('code_account')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lap_bulanan');
    }
};


