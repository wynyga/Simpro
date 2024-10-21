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
        Schema::create('tipe_rumah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perumahan')->constrained('perumahan')->onDelete('cascade');
            $table->string('tipe_rumah');
            $table->decimal('luas_bangunan', 10, 2);
            $table->decimal('luas_kavling', 10, 2);
            $table->decimal('harga_standar_tengah', 15, 2);
            $table->decimal('harga_standar_sudut', 15, 2);
            $table->decimal('penambahan_bangunan', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipe_rumah');
    }
};
