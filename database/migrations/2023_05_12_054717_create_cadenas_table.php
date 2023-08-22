<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadenasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadenas', function (Blueprint $table) {
            $table->id();            
            $table->string('codigo_laboratorio',10);
            $table->string('numero_grupo',15)->nullable();
            $table->string('numero_proceso',15)->nullable();
            $table->string('numero_orden_servicio',15)->nullable();
            $table->string('estacion',100)->nullable();
            $table->date('fecha_muestreo')->nullable();
            $table->string('tipo_muestra',50)->nullable();
            $table->foreignId('id_empresa')->nullable();
            $table->string('nombre_empresa',150)->nullable();
            $table->foreignId('id_pais')->nullable();
            $table->json('informacion_adicional');
            $table->timestamps();
            $table->index('id_pais');
            $table->index('id_empresa');
            $table->foreign('id_pais')->references('id')->on('regiones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadenas');
    }
}
