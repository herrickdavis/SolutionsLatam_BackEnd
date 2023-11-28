<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoraMuestreoToCadenasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadenas', function (Blueprint $table) {
            $table->time('hora_muestreo')->nullable()->after('fecha_muestreo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadenas', function (Blueprint $table) {
            $table->dropColumn('hora_muestreo');
        });
    }
}
