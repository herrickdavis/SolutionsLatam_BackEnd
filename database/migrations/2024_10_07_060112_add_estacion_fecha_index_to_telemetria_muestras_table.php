<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstacionFechaIndexToTelemetriaMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telemetria_muestras', function (Blueprint $table) {
            $table->index(['estacion_id', 'fecha_muestreo'], 'idx_estacion_fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telemetria_muestras', function (Blueprint $table) {
            $table->dropIndex('idx_estacion_fecha');
        });
    }
}
