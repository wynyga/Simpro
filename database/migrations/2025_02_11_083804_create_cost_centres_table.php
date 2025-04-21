<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cost_centres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');
            $table->string('cost_centre_code')->index(); // tambahkan index di sini
            $table->string('description');
            $table->string('cost_code');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('cost_centres');
    }
};

