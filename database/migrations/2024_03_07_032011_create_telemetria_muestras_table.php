<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_muestras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estacion_id');
            $table->dateTime('fecha_muestreo', $precision = 0);
            $table->string('nombre_archivo', 50);
            $table->foreign('estacion_id')->references('id')->on('telemetria_estacions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telemetria_muestras');
    }
}
