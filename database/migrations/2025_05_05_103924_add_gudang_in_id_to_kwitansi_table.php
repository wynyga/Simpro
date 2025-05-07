<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kwitansi', function (Blueprint $table) {
            $table->foreignId('gudang_in_id')->nullable()->constrained('gudang_in')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('kwitansi', function (Blueprint $table) {
            $table->dropForeign(['gudang_in_id']);
            $table->dropColumn('gudang_in_id');
        });
    }
    
};
