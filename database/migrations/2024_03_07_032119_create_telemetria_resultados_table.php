<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaResultadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('muestra_id');
            $table->foreignId('parametro_id');
            $table->string("resultado",15)->nullable();
            $table->foreignId('unidad_id')->nullable();
            $table->foreignId('abreviatura_id')->nullable();
            $table->foreign('muestra_id')->references('id')->on('telemetria_muestras');
            $table->foreign('parametro_id')->references('id')->on('telemetria_parametros');
            $table->foreign('unidad_id')->references('id')->on('telemetria_unidads');
            $table->foreign('abreviatura_id')->references('id')->on('telemetria_abreviatura_procesamientos');
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
        Schema::dropIfExists('telemetria_resultados');
    }
}
