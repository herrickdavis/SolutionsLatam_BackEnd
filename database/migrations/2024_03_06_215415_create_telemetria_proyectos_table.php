<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_proyecto',100);
            $table->foreignId('id_empresa');
            $table->timestamps();
            $table->index('id_empresa');
            $table->foreign('id_empresa')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telemetria_proyectos');
    }
}
