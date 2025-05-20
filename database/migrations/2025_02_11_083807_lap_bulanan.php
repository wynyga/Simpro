<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lap_bulanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cost_tee_id');
            $table->foreign('cost_tee_id')->references('id')->on('cost_tees')->onDelete('cascade');
            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun'); 
            $table->string('status')->default('pending');
            $table->decimal('jumlah', 15, 2); 
            $table->string('jenis_transaksi')->nullable();
            $table->string('code_account')->index(); 

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lap_bulanan');
    }
};
