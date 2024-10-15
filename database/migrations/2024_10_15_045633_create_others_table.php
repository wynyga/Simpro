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
        Schema::create('others', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable();
            $table->string('nama_barang');
            $table->string('uty');
            $table->string('satuan');
            $table->decimal('harga_satuan', 18, 2);
            $table->integer('stock_bahan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('others');
    }
};
