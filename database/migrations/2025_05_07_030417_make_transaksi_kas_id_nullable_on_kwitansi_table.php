<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('kwitansi', function (Blueprint $table) {
            $table->unsignedBigInteger('transaksi_kas_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('kwitansi', function (Blueprint $table) {
            $table->unsignedBigInteger('transaksi_kas_id')->nullable(false)->change();
        });
    }
};

