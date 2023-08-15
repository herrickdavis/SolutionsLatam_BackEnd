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
            $table->string('codigo_laboratorio',100);
            $table->string('numero_grupo',100);
            $table->string('numero_proceso',100);
            $table->string('numero_orden_servicio',100);
            $table->string('estacion',100);
            $table->string('fecha_muestreo',100);
            $table->string('tipo_muestra',100);
            $table->json('informacion_adicional');
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
        Schema::dropIfExists('cadenas');
    }
}
