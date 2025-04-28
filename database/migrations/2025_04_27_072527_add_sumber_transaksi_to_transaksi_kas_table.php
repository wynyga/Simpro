<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->enum('sumber_transaksi', ['cost_code', 'penjualan'])->nullable()->after('kode');
            $table->unsignedBigInteger('keterangan_transaksi_id')->nullable()->after('sumber_transaksi');
        });
    }

    public function down()
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropColumn(['sumber_transaksi', 'keterangan_transaksi_id']);
        });
    }
};
