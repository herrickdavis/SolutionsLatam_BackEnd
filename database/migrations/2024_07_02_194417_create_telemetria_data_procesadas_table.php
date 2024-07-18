<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaDataProcesadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_data_procesadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estacion_id');
            $table->date('fecha_muestreo');
            $table->foreignId('parametro_id');
            $table->string("resultado", 15)->nullable();
            $table->string('unidad_id', 10)->nullable();
            $table->foreignId('estado_id')->nullable();
            $table->index('estacion_id');
            $table->index('parametro_id');
            $table->index('unidad_id');
            $table->foreign('estacion_id')->references('id')->on('telemetria_estacions');
            $table->foreign('parametro_id')->references('id')->on('telemetria_parametros');
            $table->foreign('estado_id')->references('id')->on('telemetria_estado_resultados');
            $table->timestamps();

            $table->unique(['estacion_id', 'fecha_muestreo', 'parametro_id'], 'estacion_fecha_muestro_parametro_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telemetria_data_procesadas');
    }
}
