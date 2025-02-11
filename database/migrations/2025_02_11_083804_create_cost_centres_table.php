<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cost_centres', function (Blueprint $table) {
            $table->id();
            $table->string('cost_centre_code')->unique(); // KO010
            $table->string('description'); // Pembiayaan Project
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cost_centres');
    }
};

