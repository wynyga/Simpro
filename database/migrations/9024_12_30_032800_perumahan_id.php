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
        // Menambahkan kolom perumahan_id pada tabel stock
        Schema::table('day_work', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });
        
        Schema::table('equipment', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('land_stone_sand', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('cement', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('rebar', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('wood', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('roof_ceiling_tile', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('keramik_floor', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('paint_glass_wallpaper', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('others', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('oil_chemical_perekat', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('sanitary', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('piping_pump', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('lighting', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        // Tabel gudang_in
        Schema::table('gudang_in', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        // Tabel gudang_out
        Schema::table('gudang_out', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });

        // Tabel transaksi_kas
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });
        
        //Tabel user_perumahan
        Schema::table('user_perumahan', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });  
        
        //Tabel transaksi
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        }); 
        
        //Tabel cost centres
        Schema::table('cost_centres', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });   

        //Tabel cost elements
        Schema::table('cost_elements', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });   

        //Tabel cost tees
        Schema::table('cost_tees', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });   

        //Tabel cost structures
        Schema::table('cost_structures', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });   

        //Tabel laporan bulanan
        Schema::table('lap_bulanan', function (Blueprint $table) {
            $table->foreignId('perumahan_id')->after('id')->constrained('perumahan')->onDelete('cascade');
        });     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropForeign(['perumahan_id']);
            $table->dropColumn('perumahan_id');
        });

        Schema::table('gudang_in', function (Blueprint $table) {
            $table->dropForeign(['perumahan_id']);
            $table->dropColumn('perumahan_id');
        });

        Schema::table('gudang_out', function (Blueprint $table) {
            $table->dropForeign(['perumahan_id']);
            $table->dropColumn('perumahan_id');
        });

        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropForeign(['perumahan_id']);
            $table->dropColumn('perumahan_id');
        });
        
        Schema::table('blok_unit', function (Blueprint $table) {
            $table->dropForeign(['perumahan_id']);
            $table->dropColumn('perumahan_id');
        });
        
        Schema::table('user_perumahan', function (Blueprint $table) {
            $table->dropForeign(['perumahan_id']);
            $table->dropColumn('perumahan_id');
        });
        
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['perumahan_id']);
            $table->dropColumn('perumahan_id');
        });
        
    }
};
