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
        Schema::create('sanitary', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->virtualAs("CONCAT('SANI120-', LPAD(id, 2, '0'))");
            $table->string('jenis_tenaga_peralatan');
            $table->string('uty');
            $table->string('satuan');
            $table->decimal('harga_satuan', 18, 2);
            $table->integer('stock_bahan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sanitary');
    }
};
