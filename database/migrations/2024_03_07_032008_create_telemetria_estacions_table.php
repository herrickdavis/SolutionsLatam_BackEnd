<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaEstacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_estacions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_estacion', 50);
            $table->foreignId('id_empresa');
            $table->foreignId('id_proyecto_telemetria');
            $table->string('este', 10);
            $table->string('norte', 10);
            $table->string('altitud', 10);
            $table->string('zona', 5);
            $table->string('zona_descripcion', 50);
            $table->string('descripcion', 250);
            $table->timestamps();
            $table->unique(['nombre_estacion', 'id_empresa', 'id_proyecto_telemetria'], 'nombre_estacion_id_empresa_id_proyecto_telemetria_unique');
            $table->index('id_empresa');
            $table->foreign('id_empresa')->references('id')->on('empresas');
            $table->index('id_proyecto_telemetria');
            $table->foreign('id_proyecto_telemetria')->references('id')->on('telemetria_proyectos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telemetria_estacions');
    }
}
